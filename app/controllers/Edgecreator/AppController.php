<?php

namespace DmServer\Controllers\Edgecreator;

use DmServer\Controllers\AbstractController;
use DmServer\Controllers\UnexpectedInternalCallResponseException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Radebatz\Silex2Swagger\Swagger\Annotations as S2S;
use Swagger\Annotations as SWG;

/**
 * @S2S\Controller(prefix="/edgecreator",
 *   @SWG\Parameter(
 *     name="x-dm-version",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Parameter(
 *     name="x-dm-user",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Parameter(
 *     name="x-dm-pass",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Response(response=200),
 *   @SWG\Response(response=401, description="User not authorized"),
 *   @SWG\Response(response="default", description="Error")
 * ),
 * @SLX\Before("DmServer\RequestInterceptor::checkVersion")
 * @SLX\Before("DmServer\RequestInterceptor::authenticateUser")
 */
class AppController extends AbstractController
{

    /**
     * @SLX\Route(
     *   @SLX\Request(method="PUT", uri="step/{publicationcode}/{stepnumber}"),
     *   @SWG\Parameter(
     *     name="publicationcode",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="stepnumber",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="functionname",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="optionname",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="optionvalue",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="firstissuenumber",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="lastissuenumber",
     *     in="query",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="publicationcode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *	 @SLX\Assert(variable="stepnumber", regex="^(?P<stepnumber_regex>\-?\d+)$")
     * )
     * @param Application $app
     * @param Request     $request
     * @param string      $publicationcode
     * @param string      $stepnumber
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function addStep (Application $app, Request $request, $publicationcode, $stepnumber) {
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
                self::callInternal($app, '/edgecreator/value', 'PUT', [
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

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="v2/model")
     * )
     * @param Application $app
     * @return Response
     */
    public function getV2MyModels(Application $app) {
        return self::callInternal($app, '/edgecreator/v2/model', 'GET');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="v2/model/{modelId}"),
     *   @SWG\Parameter(
     *     name="modelId",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param string $modelId
     * @return Response
     */
    public function getModel(Application $app, $modelId) {
        return self::callInternal($app, "/edgecreator/v2/model/$modelId", 'GET');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="v2/model/editedbyother/all")
     * )
     * @param Application $app
     * @return Response
     */
    public function getModelsEditedByOthers(Application $app) {
        return self::callInternal($app, '/edgecreator/v2/model/editedbyother/all', 'GET');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="v2/model/unassigned/all")
     * )
     * @param Application $app
     * @return Response
     */
    public function getUnassignedModels(Application $app) {
        return self::callInternal($app, '/edgecreator/v2/model/unassigned/all', 'GET');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="v2/model/{publicationcode}/{issuenumber}"),
     *   @SWG\Parameter(
     *     name="publicationcode",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="issuenumber",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="publicationcode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *	 @SLX\Assert(variable="issuenumber", regex="^(?P<issuenumber_regex>[-A-Z0-9 ]+)$")
     * )
     * @param Application $app
     * @param string $publicationcode
     * @param string $issuenumber
     * @return Response
     */
    public function getV2Model(Application $app, $publicationcode, $issuenumber) {
        return self::callInternal($app, "/edgecreator/v2/model/$publicationcode/$issuenumber", 'GET');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="PUT", uri="v2/model/{publicationcode}/{issuenumber}/{iseditor}"),
     *   @SWG\Parameter(
     *     name="publicationcode",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="issuenumber",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="iseditor",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="publicationcode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *	 @SLX\Assert(variable="issuenumber", regex="^(?P<issuenumber_regex>[-A-Z0-9 ]+)$"),
     *	 @SLX\Value(variable="iseditor", default=0)
     * )
     * @param Application $app
     * @param string      $publicationcode
     * @param string      $issuenumber
     * @param string      $iseditor
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function createModel(Application $app, $publicationcode, $issuenumber, $iseditor) {
        try {
            $modelId = self::getResponseIdFromServiceResponse(
                self::callInternal($app, "/edgecreator/v2/model/$publicationcode/$issuenumber/$iseditor", 'PUT'),
                'modelid'
            );

            return new JsonResponse(['modelid' => $modelId]);
        }
        catch (UnexpectedInternalCallResponseException $e) {
            return new Response($e->getContent(), $e->getStatusCode());
        }
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="POST", uri="v2/model/clone/to/{publicationcode}/{issuenumber}"),
     *   @SWG\Parameter(
     *     name="publicationcode",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="issuenumber",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="steps",
     *     in="query",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="publicationcode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *	 @SLX\Assert(variable="issuenumber", regex="^(?P<issuenumber_regex>[-A-Z0-9 ]+)$")
     * )
     * @param Application $app
     * @param Request     $request
     * @param string      $publicationcode
     * @param string      $issuenumber
     * @return Response
     * @throws \InvalidArgumentException
     * @throws UnexpectedInternalCallResponseException
     */
    public function cloneSteps(Application $app, Request $request, $publicationcode, $issuenumber) {
        /** @var string[] $steps */
        $steps = $request->request->get('steps');

        $targetModelId = null;
        $deletedSteps = 0;

        try {
            // Target model already exists
            $targetModelId = self::getResponseIdFromServiceResponse(
                self::callInternal($app, "/edgecreator/v2/model/$publicationcode/$issuenumber", 'GET'),
                'id'
            );
            try {
                $deletedSteps = self::getResponseIdFromServiceResponse(
                    self::callInternal($app, "/edgecreator/v2/model/$targetModelId/empty", 'POST'),
                    'steps'
                )->deleted;
            }
            catch(UnexpectedInternalCallResponseException $e) {
                return new Response($e->getContent(), $e->getStatusCode());
            }
        }
        catch(UnexpectedInternalCallResponseException $e) {
            if ($e->getStatusCode() === Response::HTTP_NO_CONTENT) {
                $targetModelId = self::getResponseIdFromServiceResponse(
                    self::callInternal($app, "/edgecreator/v2/model/$publicationcode/$issuenumber/1", 'PUT'),
                    'modelid'
                );
            }
            else {
                return new Response($e->getContent(), $e->getStatusCode());
            }
        }
        finally {
            self::callInternal($app, "/edgecreator/v2/model/assign/$targetModelId", 'POST');

            $valueIds = [];
            foreach($steps as $stepNumber => $stepOptions) {
                $valueIds[$stepNumber] = self::getResponseIdFromServiceResponse(
                    self::callInternal($app, "/edgecreator/v2/step/$targetModelId/$stepNumber", 'PUT', [
                        'newFunctionName' => $stepOptions['stepfunctionname'],
                        'options' => $stepOptions['options']
                    ]),
                    'valueids'
                );
            }
            return new JsonResponse([
                'modelid' => $targetModelId,
                'valueids' => $valueIds,
                'deletedsteps' => $deletedSteps
            ]);
        }
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="POST", uri="v2/step/{modelid}/{stepnumber}"),
     *   @SWG\Parameter(
     *     name="modelid",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="stepnumber",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="stepfunctionname",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="options",
     *     in="query",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelid", regex="^(?P<modelid_regex>\d+)$"),
     *	 @SLX\Assert(variable="stepnumber", regex="^(?P<stepnumber_regex>\-?\d+)$")
     * )
     * @param Application $app
     * @param Request     $request
     * @param string      $modelid
     * @param string      $stepnumber
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function createOrUpdateStep(Application $app, Request $request, $modelid, $stepnumber) {
        $stepFunctionName = $request->request->get('stepfunctionname');
        $optionValues = $request->request->get('options');

        try {
            $valueIds = self::getResponseIdFromServiceResponse(
                self::callInternal($app, "/edgecreator/v2/step/$modelid/$stepnumber", 'PUT', [
                    'newFunctionName' => $stepFunctionName,
                    'options' => $optionValues
                ]),
                'valueids'
            );

            return new JsonResponse(['valueids' => $valueIds]);
        }
        catch (UnexpectedInternalCallResponseException $e) {
            return new Response($e->getContent(), $e->getStatusCode());
        }
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="POST", uri="v2/step/shift/{modelid}/{stepnumber}/{isincludingthisstep}"),
     *   @SWG\Parameter(
     *     name="modelid",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="stepnumber",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="isincludingthisstep",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelid", regex="^(?P<modelid_regex>\d+)$"),
     *	 @SLX\Assert(variable="stepnumber", regex="^(?P<stepnumber_regex>\-?\d+)$")
     * )
     * @param Application $app
     * @param string $modelid
     * @param string $stepnumber
     * @param $isincludingthisstep
     * @return Response
     */
    public function shiftStep(Application $app, $modelid, $stepnumber, $isincludingthisstep) {
        return self::callInternal($app, "/edgecreator/v2/step/shift/$modelid/$stepnumber/$isincludingthisstep", 'POST');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="POST", uri="v2/step/clone/{modelid}/{stepnumber}/to/{newstepnumber}"),
     *   @SWG\Parameter(
     *     name="modelid",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="stepnumber",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="newstepnumber",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelid", regex="^(?P<modelid_regex>\d+)$"),
     *	 @SLX\Assert(variable="stepnumber", regex="^(?P<stepnumber_regex>\-?\d+)$"),
     *	 @SLX\Assert(variable="newstepnumber", regex="^(?&stepnumber_regex)$")
     * )
     * @param Application $app
     * @param string $modelid
     * @param string $stepnumber
     * @param string $newstepnumber
     * @return Response
     */
    public function cloneStep(Application $app, $modelid, $stepnumber, $newstepnumber) {
        return self::callInternal($app, "/edgecreator/v2/step/clone/$modelid/$stepnumber/$newstepnumber", 'POST');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="DELETE", uri="v2/step/{modelid}/{stepnumber}"),
     *   @SWG\Parameter(
     *     name="modelid",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="stepnumber",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelid", regex="^(?P<modelid_regex>\d+)$"),
     *	 @SLX\Assert(variable="stepnumber", regex="^(?P<stepnumber_regex>\-?\d+)$")
     * )
     * @param Application $app
     * @param string $modelid
     * @param string $stepnumber
     * @return Response
     */
    public function deleteStep(Application $app, $modelid, $stepnumber) {
        return self::callInternal($app, "/edgecreator/v2/step/$modelid/$stepnumber", 'DELETE');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="PUT", uri="myfontspreview"),
     *   @SWG\Parameter(
     *     name="font",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="fgColor",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="bgColor",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="width",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="text",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="precision",
     *     in="query",
     *     required=true
     *   )
     * )
     * @param Application $app
     * @param Request $request
     * @return Response
     * @throws UnexpectedInternalCallResponseException
     */
    public function storeMyFontsPreview(Application $app, Request $request) {
        $previewId = self::getResponseIdFromServiceResponse(
            self::callInternal($app, '/edgecreator/myfontspreview', 'PUT', [
                'font' => $request->request->get('font'),
                'fgColor' => $request->request->get('fgColor'),
                'bgColor' => $request->request->get('bgColor'),
                'width' => $request->request->get('width'),
                'text' => $request->request->get('text'),
                'precision' => $request->request->get('precision'),
            ]),
            'previewid'
        );

        return new JsonResponse(['previewid' => $previewId]);
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="DELETE", uri="myfontspreview/{previewid}"),
     *   @SWG\Parameter(
     *     name="previewid",
     *     in="query",
     *     required=true
     *   ),
     * )
     * @param Application $app
     * @param string $previewid
     * @return Response
     */
    public function deleteMyFontsPreview(Application $app, $previewid) {
        return self::callInternal($app, "/edgecreator/myfontspreview/$previewid", 'DELETE');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="POST", uri="model/v2/{modelid}/deactivate"),
     *   @SWG\Parameter(
     *     name="modelid",
     *     in="query",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelid", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param string $modelid
     * @return Response
     */
    public function deactivateModel(Application $app, $modelid) {
        return self::callInternal($app, "/edgecreator/model/v2/$modelid/deactivate", 'POST');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="POST", uri="model/v2/{modelid}/readytopublish/{isreadytopublish}"),
     *   @SWG\Parameter(
     *     name="modelid",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="isreadytopublish",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="designers",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="photographers",
     *     in="query",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelid", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param Request $request
     * @param string $modelid
     * @param string $isreadytopublish
     * @return Response
     */
    public function setModelAsReadyToBePublished(Application $app, Request $request, $modelid, $isreadytopublish) {
        return self::callInternal($app, "/edgecreator/model/v2/$modelid/readytopublish/$isreadytopublish", 'POST', [
            'designers' => $request->request->get('designers'),
            'photographers' => $request->request->get('photographers')
        ]);
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="PUT", uri="model/v2/{modelid}/photo/main"),
     *   @SWG\Parameter(
     *     name="modelid",
     *     in="path",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="photoname",
     *     in="query",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelid", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param Request $request
     * @param string $modelid
     * @return Response
     */
    public function setModelMainPhoto(Application $app, Request $request, $modelid) {
        return self::callInternal($app, "/edgecreator/model/v2/$modelid/photo/main", 'PUT', [
            'photoname' => $request->request->get('photoname')
        ]);
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="model/v2/{modelid}/photo/main"),
     *   @SWG\Parameter(
     *     name="modelid",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="modelid", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param string $modelid
     * @return Response
     */
    public function getModelMainPhoto(Application $app, $modelid) {
        return self::callInternal($app, "/edgecreator/model/v2/$modelid/photo/main");
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="multiple_edge_photo/today")
     * )
     * @param Application $app
     * @return Response
     */
    public function getMultipleEdgePhotosFromToday(Application $app) {
        return self::callInternal($app, '/edgecreator/multiple_edge_photo/today', 'GET');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="multiple_edge_photo/hash/{hash}"),
     *   @SWG\Parameter(
     *     name="hash",
     *     in="path",
     *     required=true
     *   )
     * )
     * @param Application $app
     * @param string $hash
     * @return Response
     */
    public function getMultipleEdgePhotoFromHash(Application $app, $hash) {
        return self::callInternal($app, "/edgecreator/multiple_edge_photo/$hash", 'GET');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="PUT", uri="multiple_edge_photo"),
     *   @SWG\Parameter(
     *     name="hash",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="filename",
     *     in="query",
     *     required=true
     *   )
     * )
     * @param Request $request
     * @param Application $app
     * @return Response
     */
    public function createMultipleEdgePhoto(Request $request, Application $app) {
        return self::callInternal($app, '/edgecreator/multiple_edge_photo', 'PUT', [
            'hash' => $request->request->get('hash'),
            'filename' => $request->request->get('filename')
        ]);
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="elements/images/{nameSubString}")
     * )
     * @param Application $app
     * @param string      $nameSubString
     * @return Response
     * @throws \Exception
     */
    public function getElementImagesByNameSubstring(Application $app, $nameSubString) {
        return self::callInternal($app, "/edgecreator/elements/images/$nameSubString", 'GET');
    }
}
