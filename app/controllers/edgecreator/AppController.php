<?php

namespace DmServer\Controllers\EdgeCreator;

use DmServer\Controllers\AbstractController;
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

                $optionIdResponse = self::callInternal($app, "/edgecreator/step/$publicationcode/$stepnumber", 'PUT', [
                    'functionname' => $functionName,
                    'optionName' => $optionName
                ]);

                if ($optionIdResponse->getStatusCode() !== Response::HTTP_OK) {
                    return new Response($optionIdResponse->getContent(), $optionIdResponse->getStatusCode());
                }

                $optionId = json_decode($optionIdResponse->getContent())->optionid;

                $valueIdResponse = self::callInternal($app, "/edgecreator/value", 'PUT', [
                    'optionid' => $optionId,
                    'optionvalue' => $optionValue
                ]);

                if ($valueIdResponse->getStatusCode() !== Response::HTTP_OK) {
                    return new Response($valueIdResponse->getContent(), $valueIdResponse->getStatusCode());
                }

                $valueId = json_decode($valueIdResponse->getContent())->valueid;

                $intervalIdResponse = self::callInternal($app, "/edgecreator/interval/$valueId/$firstIssueNumber/$lastIssueNumber", 'PUT');

                if ($intervalIdResponse->getStatusCode() !== Response::HTTP_OK) {
                    return new Response($intervalIdResponse->getContent(), $intervalIdResponse->getStatusCode());
                }

                $intervalId = json_decode($intervalIdResponse->getContent())->intervalid;

                return new JsonResponse(['optionid' => $optionId, 'valueid' => $valueId, 'intervalid' => $intervalId]);
            }
        )
            ->assert('publicationcode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('stepnumber', self::getParamAssertRegex('\\d+'));


        $routing->post(
            '/edgecreator/step/clone/{publicationcode}/{issuenumber}/{stepnumber}/to/{newstepnumber}',
            function (Application $app, Request $request, $publicationcode, $issuenumber, $stepnumber, $newstepnumber) {
                return self::callInternal($app, "/edgecreator/step/clone/$publicationcode/$issuenumber/$stepnumber/$newstepnumber", 'POST');
            }
        )
            ->assert('publicationcode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('stepnumber', self::getParamAssertRegex('\\d+'))
            ->assert('newstepnumber', self::getParamAssertRegex('\\d+'));
    }
}
