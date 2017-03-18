<?php

namespace DmServer\Controllers\EdgeCreator;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use EdgeCreator\Models\EdgecreatorIntervalles;
use EdgeCreator\Models\EdgecreatorModeles2;
use EdgeCreator\Models\EdgecreatorValeurs;
use EdgeCreator\Models\TranchesEnCoursModeles;
use EdgeCreator\Models\TranchesEnCoursValeurs;
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
            '/internal/edgecreator/step/{publicationCode}/{stepNumber}',
            function (Request $request, Application $app, $publicationCode, $stepNumber) {
                return AbstractController::return500ErrorOnException($app, function() use ($request, $publicationCode, $stepNumber) {
                    $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                    list($country, $publication) = explode('/', $publicationCode);
                    $functionName = $request->request->get('functionname');
                    $optionName = $request->request->get('optionname');

                    $model = new EdgecreatorModeles2();
                    $model->setPays($country);
                    $model->setMagazine($publication);
                    $model->setOrdre($stepNumber);
                    $model->setNomFonction($functionName);
                    $model->setOptionNom($optionName);

                    $em->persist($model);
                    $em->flush();

                    return new JsonResponse(['optionid' => $model->getId()]);
                });
            }
        )
            ->assert('publicationCode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('stepNumber', self::getParamAssertRegex('\\d+'));

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

        $routing->post(
            '/internal/edgecreator/step/clone/{modelId}/{stepNumber}/{newStepNumber}',
            function (Request $request, Application $app, $modelId, $stepNumber, $newStepNumber) {
                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                $values = $em->getRepository(TranchesEnCoursValeurs::class)->findBy([
                    'idModele' => $modelId,
                    'ordre' => $stepNumber
                ]);

                $newValues = array_map(function(TranchesEnCoursValeurs $value) use ($em, $newStepNumber) {
                    $newValue = new TranchesEnCoursValeurs();
                    $newValue->setIdModele($value->getIdModele());
                    $newValue->setNomFonction($value->getNomFonction());
                    $newValue->setOptionNom($value->getOptionNom());
                    $newValue->setOptionValeur($value->getOptionValeur());
                    $newValue->setOrdre($newStepNumber);
                    $em->persist($newValue);
                }, $values);

                $em->flush();

            }
        )
            ->assert('stepNumber', self::getParamAssertRegex('\\d+'))
            ->assert('newStepNumber', self::getParamAssertRegex('\\d+'));

        $routing->get(
            '/internal/edgecreator/step/{publicationCode}/{issueNumber}/{stepNumber}/{byCurrentUser}',
            function (Request $request, Application $app, $publicationCode, $issueNumber, $stepNumber, $byCurrentUser) {
                return AbstractController::return500ErrorOnException($app, function() use ($app, $publicationCode, $issueNumber, $stepNumber, $byCurrentUser) {
                    $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                    list($country, $publication) = explode('/', $publicationCode);

                    $filter = [
                        'pays' => $country,
                        'magazine' => $publication,
                        'numero' => $issueNumber
                    ];

                    if ($byCurrentUser === '1') {
                        $filter = array_merge($filter, [
                            'username' => self::getSessionUser($app)['username'],
                            'active' => 1
                        ]);
                    }

                    $model = $em->getRepository(TranchesEnCoursModeles::class)->findOneBy($filter);

                    return new JsonResponse(json_encode($model));
                });
            }
        )
            ->assert('publicationCode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('issueNumber', self::getParamAssertRegex(\Coa\Models\BaseModel::ISSUE_CODE_VALIDATION))
            ->assert('stepNumber', self::getParamAssertRegex('\\d+'))
            ->value('byCurrentUser', false);
    }
}
