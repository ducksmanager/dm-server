<?php

namespace DmServer\Controllers\EdgeCreator;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use EdgeCreator\Models\EdgecreatorIntervalles;
use EdgeCreator\Models\EdgecreatorModeles2;
use EdgeCreator\Models\EdgecreatorValeurs;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InternalController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->put(
            '/internal/edgecreator/step/{publicationcode}/{stepnumber}',
            function (Request $request, Application $app, $publicationcode, $stepnumber) {
                return AbstractController::return500ErrorOnException($app, function() use ($request, $publicationcode, $stepnumber) {
                    $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                    list($country, $magazine) = explode('/', $publicationcode);
                    $functionName = $request->request->get('functionname');
                    $optionName = $request->request->get('optionname');

                    $model = new EdgecreatorModeles2();
                    $model->setPays($country);
                    $model->setMagazine($magazine);
                    $model->setOrdre($stepnumber);
                    $model->setNomFonction($functionName);
                    $model->setOptionNom($optionName);

                    $em->persist($model);
                    $em->flush();

                    return new JsonResponse(['optionid' => $model->getId()]);
                });
            }
        )
            ->assert('publicationcode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('stepnumber', self::getParamAssertRegex('\\d+'));

        $routing->put(
            '/internal/edgecreator/value',
            function (Request $request, Application $app) {
                return AbstractController::return500ErrorOnException($app, function() use ($request) {
                    $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                    $optionId = $request->request->get('optionid');
                    $optionValue = $request->request->get('optionvalue');

                    $value = new EdgecreatorValeurs();
                    $value->setIdOption($optionId);
                    $value->setOptionValeur($optionValue);

                    $em->persist($value);
                    $em->flush();

                    return new JsonResponse(['valueid' => $value->getId()]);
                });
            }
        );

        $routing->put(
            '/internal/edgecreator/interval/{valueId}/{firstIssueNumber}/{lastIssueNumber}',
            function (Request $request, Application $app, $valueId, $firstIssueNumber, $lastIssueNumber) {
                return AbstractController::return500ErrorOnException($app, function() use ($app, $valueId, $firstIssueNumber, $lastIssueNumber) {
                    $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                    $interval = new EdgecreatorIntervalles();

                    $interval->setIdValeur($valueId);
                    $interval->setNumeroDebut($firstIssueNumber);
                    $interval->setNumeroFin($lastIssueNumber);
                    $interval->setUsername(self::getSessionUser($app)['id']);

                    $em->persist($interval);
                    $em->flush();

                    return new JsonResponse(['intervalid' => $interval->getId()]);
                });
            }
        );
    }
}
