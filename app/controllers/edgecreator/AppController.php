<?php

namespace DmServer\Controllers\EdgeCreator;

use DmServer\Controllers\AbstractController;
use DmServer\Controllers\UnexpectedInternalCallResponseException;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{

    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->put(
            '/edgecreator/step/{publicationcode}/{stepnumber}',
            function (Application $app, Request $request, $publicationcode, $stepnumber) {
                $functionName = $request->request->get('functionname');
                $optionName = $request->request->get('optionname');
                $optionValue = $request->request->get('optionvalue');
                $firstIssueNumber = $request->request->get('firstissuenumber');
                $lastIssueNumber = $request->request->get('lastissuenumber');

                try {
                    $optionId = self::getResponseIdFromServiceResponse(
                        self::callInternal($app, "/edgecreator/step/$publicationcode/$stepnumber", 'PUT', [
                            'functionname' => $functionName,
                            'optionname' => $optionName
                        ]),
                        'optionid');

                    $valueId = self::getResponseIdFromServiceResponse(
                        self::callInternal($app, "/edgecreator/value", 'PUT', [
                            'optionid' => $optionId,
                            'optionvalue' => $optionValue
                        ]),
                        'valueid'
                    );

                    $intervalId = self::getResponseIdFromServiceResponse(
                        self::callInternal($app, "/edgecreator/interval/$valueId/$firstIssueNumber/$lastIssueNumber", 'PUT'),
                        'intervalid'
                    );

                    return new JsonResponse(['optionid' => $optionId, 'valueid' => $valueId, 'intervalid' => $intervalId]);
                }
                catch (UnexpectedInternalCallResponseException $e) {
                    return new Response($e->getContent(), $e->getStatusCode());
                }
            }
        )
            ->assert('publicationcode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('stepnumber', self::getParamAssertRegex('\\d+'));


        $routing->post(
            '/edgecreator/step/shift/{publicationcode}/{issuenumber}/{stepnumber}/{isincludingthisstep}',
            function (Application $app, Request $request, $publicationcode, $issuenumber, $stepnumber, $isincludingthisstep) {

                try {
                    $modelId = self::getResponseIdFromServiceResponse(
                        self::callInternal($app, "/edgecreator/step/$publicationcode/$issuenumber/1"),
                        'modelid'
                    );
                }
                catch (UnexpectedInternalCallResponseException $e) {
                    return new Response($e->getContent(), $e->getStatusCode());
                }

                return self::callInternal($app, "/edgecreator/step/shift/$modelId/$stepnumber/$isincludingthisstep", 'POST');
            }
        )
            ->assert('publicationcode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('stepnumber', self::getParamAssertRegex('\\d+'))
            ->assert('newstepnumber', self::getParamAssertRegex('\\d+'));

        $routing->post(
            '/edgecreator/step/clone/{publicationcode}/{issuenumber}/{stepnumber}/to/{newstepnumber}',
            function (Application $app, Request $request, $publicationcode, $issuenumber, $stepnumber, $newstepnumber) {
                try {
                    $modelId = self::getResponseIdFromServiceResponse(
                        self::callInternal($app, "/edgecreator/step/$publicationcode/$issuenumber/1"),
                        'modelid'
                    );
                }
                catch (UnexpectedInternalCallResponseException $e) {
                    return new Response($e->getContent(), $e->getStatusCode());
                }

                return self::callInternal($app, "/edgecreator/step/clone/$modelId/$stepnumber/$newstepnumber", 'POST');
            }
        )
        ->assert('publicationcode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
        ->assert('stepnumber', self::getParamAssertRegex('\\d+'))
        ->assert('newstepnumber', self::getParamAssertRegex('\\d+'));


        $routing->put(
            '/edgecreator/myfontspreview',
            function (Application $app, Request $request) {
                $font = $request->request->get('font');
                $fgColor = $request->request->get('fgColor');
                $bgColor = $request->request->get('bgColor');
                $width = $request->request->get('width');
                $text = $request->request->get('text');
                $precision = $request->request->get('precision');

                $previewIdResponse = self::callInternal($app, "/edgecreator/myfontspreview", 'PUT', [
                    'font' => $font,
                    'fgColor' => $fgColor,
                    'bgColor' => $bgColor,
                    'width' => $width,
                    'text' => $text,
                    'precision' => $precision,
                ]);

                $previewId = json_decode($previewIdResponse->getContent())->previewid;

                return new JsonResponse(['previewid' => $previewId]);
            }
        );

        $routing->post(
            '/edgecreator/model/v2/{modelid}/deactivate',
            function (Application $app, Request $request, $modelid) {
                return self::callInternal($app, "/edgecreator/model/v2/$modelid/deactivate", 'POST');
            }
        );

        $routing->post(
            '/edgecreator/model/v2/{modelid}/readytopublish/{isreadytopublish}',
            function (Application $app, Request $request, $modelid, $isreadytopublish) {
                return self::callInternal($app, "/edgecreator/model/v2/$modelid/readytopublish/$isreadytopublish", 'POST');
            }
        );

        $routing->put(
            '/edgecreator/model/v2/{modelid}/photo/main',
            function (Application $app, Request $request, $modelid) {
                return self::callInternal($app, "/edgecreator/model/v2/$modelid/photo/main", 'PUT', [
                    'photoname' => $request->request->get('photoname')
                ]);
            }
        );

        $routing->delete(
            '/edgecreator/myfontspreview/{previewid}',
            function (Application $app, Request $request, $previewid) {
                return self::callInternal($app, "/edgecreator/myfontspreview/$previewid", 'DELETE');
            }
        );
    }
}
