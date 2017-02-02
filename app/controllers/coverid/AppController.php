<?php

namespace DmServer\Controllers\Coverid;

use CoverId\Models\BaseModel;
use DmServer\Controllers\AbstractController;
use DmServer\ModelHelper;
use DmServer\SimilarImagesHelper;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AppController extends AbstractController
{
    static $uploadFileName = 'wtd_jpg';
    static $uploadDestination = ['/tmp', 'test.jpg'];

    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/cover-id/download/{issueUrl}',
            function (Application $app, Request $request, $issueUrl) {
                /** @var BinaryFileResponse $internalRequestResponse */
                $internalRequestResponse = self::callInternal($app, '/cover-id/download', 'GET', [$issueUrl]);
                $response = new Response(file_get_contents($internalRequestResponse->getFile()->getRealPath()));

                $disposition = $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    'cover.jpg'
                );

                $response->headers->set('Content-Disposition', $disposition);
                return $response;
            }
        )->assert('issueUrl', '.+');

        $routing->post(
            '/cover-id/search',
            function (Application $app, Request $request) {
                return AbstractController::return500ErrorOnException($app, function() use ($app, $request) {
                    $app['monolog']->addInfo('Cover ID search: start');
                    if (($nbUploaded = $request->files->count()) !== 1) {
                        return new Response('Invalid number of uploaded files : should be 1, was '.$nbUploaded, Response::HTTP_BAD_REQUEST);
                    }
                    else {
                        /** @var File $uploadedFile */
                        $uploadedFile = $request->files->get(self::$uploadFileName);
                        if (is_null($uploadedFile)) {
                            return new Response('Invalid upload file : expected file name '.self::$uploadFileName, Response::HTTP_BAD_REQUEST);
                        }
                        else {
                            $app['monolog']->addInfo('Cover ID search: upload file validation done');
                            $file = $uploadedFile->move(self::$uploadDestination[0], self::$uploadDestination[1]);
                            $app['monolog']->addInfo('Cover ID search: upload file moving done');

                            $engineResponse = SimilarImagesHelper::getSimilarImages($file);

                            $app['monolog']->addInfo('Cover ID search: processing done');

                            if (!is_null($engineResponse) && !empty($engineResponse['image_ids'])) {
                                $coverids = implode(',', $engineResponse['image_ids']);
                                $app['monolog']->addInfo('Cover ID search: matched cover IDs '.$coverids);
                                $issueCodes = ModelHelper::getUnserializedArrayFromJson(
                                    self::callInternal($app, '/cover-id/issuecodes', 'GET', [$coverids])->getContent()
                                );

                                $issueCodesStr = implode(',', $issueCodes);
                                $app['monolog']->addInfo('Cover ID search: matched issue codes '.$issueCodesStr);
                                $issues = ModelHelper::getUnserializedArrayFromJson(
                                    self::callInternal($app, '/coa/issuesbycodes', 'GET', [$issueCodesStr])->getContent()
                                );
                                $app['monolog']->addInfo('Cover ID search: matched '.count($issueCodes).' issues');

                                return new JsonResponse(ModelHelper::getSimpleArray($issues));
                            }
                            else {
                                throw new \Exception("Can't decode image similarity response");
                            }
                        }
                    }
                });
            }
        );

        $routing->get(
            '/cover-id/issuecodes/{coverids}',
            function (Application $app, Request $request, $coverids) {
                return new JsonResponse(
                    ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/cover-id/issuecodes', 'GET', [$coverids])->getContent()
                    )
                );
            }
        )->assert('coverids', self::getParamAssertRegex(BaseModel::COVER_ID_VALIDATION, 4));
    }
}
