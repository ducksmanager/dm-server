<?php

namespace Wtd;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CoverIdController extends AppController
{
    static $uploadFileName = 'WTD_jpg';
    static $uploadDestination = ['/tmp', 'test.jpg'];

    static $similarImagesEngine = 'default';

    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post(
            '/cover-id/search',
            function (Application $app, Request $request) {
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
                        try {
                            $file = $uploadedFile->move(self::$uploadDestination[0], self::$uploadDestination[1]);

                            switch(self::$similarImagesEngine) {
                                case 'mocked':
                                    $engineResponse = SimilarImagesHelper::getSimilarImagesMocked($file);
                                break;
                                default:
                                    $engineResponse = SimilarImagesHelper::getSimilarImages($file);
                            }

                            if (!is_null($engineResponse) && !empty($engineResponse['image_ids'])) {
                                $coverids = implode(',', $engineResponse['image_ids']);
                                $issueCodes = ModelHelper::getUnserializedArrayFromJson(
                                    self::callInternal($app, '/cover-id/issuecodes', 'GET', [$coverids])->getContent()
                                );

                                $issueCodesStr = implode(',', $issueCodes);
                                $issues = ModelHelper::getUnserializedArrayFromJson(
                                    self::callInternal($app, '/coa/issuesbycodes', 'GET', [$issueCodesStr])->getContent()
                                );

                                return new JsonResponse(ModelHelper::getSimpleArray($issues));
                            }
                            else {
                                return new Response("Can't decode image similarity response", Response::HTTP_INTERNAL_SERVER_ERROR);
                            }
                        }
                        catch(\Exception $e) {
                            return new Response("Exception : ".$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
        )->assert('coverids', '^([0-9]+,){0,4}[0-9]+$');
    }
}
