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
    public static $uploadFileName = 'wtd_jpg';
    public static $uploadDestination = ['/tmp', 'test.jpg'];

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
                $app['monolog']->addInfo('Cover ID search: start');
                if (($nbUploaded = $request->files->count()) !== 1) {
                    return new Response('Invalid number of uploaded files : should be 1, was ' . $nbUploaded,
                        Response::HTTP_BAD_REQUEST);
                } else {
                    /** @var File $uploadedFile */
                    $uploadedFile = $request->files->get(self::$uploadFileName);
                    if (is_null($uploadedFile)) {
                        return new Response('Invalid upload file : expected file name ' . self::$uploadFileName,
                            Response::HTTP_BAD_REQUEST);
                    } else {
                        $app['monolog']->addInfo('Cover ID search: upload file validation done');
                        $file = $uploadedFile->move(self::$uploadDestination[0], self::$uploadDestination[1]);
                        $app['monolog']->addInfo('Cover ID search: upload file moving done');

                        $engineResponse = SimilarImagesHelper::getSimilarImages($file, $app['monolog']);

                        $app['monolog']->addInfo('Cover ID search: processing done');

                        if (!is_null($engineResponse) && count($engineResponse->getImageIds()) > 0) {
                            $coverids = implode(',', $engineResponse->getImageIds());
                            $app['monolog']->addInfo('Cover ID search: matched cover IDs ' . $coverids);
                            $coverInfos = ModelHelper::getUnserializedArrayFromJson(
                                self::callInternal($app, '/cover-id/issuecodes', 'GET', [$coverids])->getContent()
                            );

                            $foundIssueCodes = array_map(function($coverInfo) {
                                return $coverInfo['issuecode'];
                            }, $coverInfos);
                            $app['monolog']->addInfo('Cover ID search: matched issue codes ' . implode(',', $foundIssueCodes));

                            $urlsStr = implode(',', array_map(function($coverInfo) {
                                return $coverInfo['url'];
                            }, $coverInfos));

//                            $issuesWithSameCover = self::getResponseIdFromServiceResponse(
//                                self::callInternal($app, "/coa/issuesbycoverurl", 'GET',
//                                    [$urlsStr]),
//                                'relatedissuecodes');

                            $issuesWithSameCover = [];

                            $issueCodesStr = implode(',',
                                array_unique(
                                    array_merge(
                                        $foundIssueCodes,
                                        array_map(/**
                                         * @param \stdClass $issue
                                         * @return mixed
                                         */
                                            function(\stdClass $issue) {
                                            return $issue->issuecode;
                                        }, $issuesWithSameCover)
                                    )
                                )
                            );

                            $issues = ModelHelper::getUnserializedArrayFromJson(
                                self::callInternal($app, '/coa/issuesbycodes', 'GET',
                                    [$issueCodesStr])->getContent()
                            );
                            $app['monolog']->addInfo('Cover ID search: matched ' . count($coverInfos) . ' issues');

                            return new JsonResponse(['issues' => ModelHelper::getSimpleArray($issues)]);
                        } else {
                            return new JsonResponse(['type' => $engineResponse->getType()]);
                        }
                    }
                }
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
