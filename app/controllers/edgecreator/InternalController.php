<?php

namespace DmServer\Controllers\EdgeCreator;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\Criteria;
use EdgeCreator\Models\EdgecreatorIntervalles;
use EdgeCreator\Models\EdgecreatorModeles2;
use EdgeCreator\Models\EdgecreatorValeurs;
use EdgeCreator\Models\ImagesMyfonts;
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
                    $model->setOrdre((int) $stepNumber);
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

        $routing->post(
            '/internal/edgecreator/step/shift/{modelId}/{stepNumber}/{isIncludingThisStep}',
            function (Request $request, Application $app, $modelId, $stepNumber, $isIncludingThisStep) {
                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                $model = $em->getRepository(TranchesEnCoursModeles::class)->find($modelId);

                $criteria = new Criteria();
                $criteria
                    ->where($criteria->expr()->andX(
                        $criteria->expr()->eq('idModele', $model)),
                        $isIncludingThisStep ==='inclusive'
                            ? $criteria->expr()->gte('ordre', $stepNumber)
                            : $criteria->expr()->gt ('ordre', $stepNumber)
                    );

                $values = $em->getRepository(TranchesEnCoursValeurs::class)->matching($criteria);

                $shifts = array_map(
                    function(TranchesEnCoursValeurs $value) use ($em) {
                        $shift = ['old' => $value->getOrdre(), 'new' => $value->getOrdre() + 1];
                        $value->setOrdre($value->getOrdre() + 1);
                        $em->persist($value);

                        return $shift;
                }, $values->toArray());

                $em->flush();

                return new JsonResponse(['shifts' => $shifts]);
            }
        )
            ->assert('stepNumber', self::getParamAssertRegex('\\d+'))
            ->assert('newStepNumber', self::getParamAssertRegex('\\d+'));

        $routing->get(
            '/internal/edgecreator/step/{publicationCode}/{issueNumber}/{byCurrentUser}',
            function (Request $request, Application $app, $publicationCode, $issueNumber, $byCurrentUser) {
                return AbstractController::return500ErrorOnException($app, function() use ($app, $publicationCode, $issueNumber, $byCurrentUser) {
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

                    return new JsonResponse(['modelid' => $model->getId()]);
                });
            }
        )
            ->assert('publicationCode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('issueNumber', self::getParamAssertRegex(\Coa\Models\BaseModel::ISSUE_NUMBER_VALIDATION))
            ->value('byCurrentUser', false);


        $routing->put(
            '/internal/edgecreator/myfontspreview',
            function (Application $app, Request $request) {
                $preview = new ImagesMyfonts();

                $preview->setFont($request->request->get('font'));
                $preview->setColor($request->request->get('fgColor'));
                $preview->setColorbg($request->request->get('bgColor'));
                $preview->setWidth($request->request->get('width'));
                $preview->setTexte($request->request->get('text'));
                $preview->setPrecision($request->request->get('precision'));

                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);
                $em->persist($preview);
                $em->flush();

                return new JsonResponse(['previewid' => $preview->getId()]);
            }
        );

        $routing->delete(
            '/internal/edgecreator/myfontspreview/{previewId}',
            function (Application $app, Request $request, $previewId) {
                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                $preview = $em->getRepository(ImagesMyfonts::class)->find($previewId);
                $em->remove($preview);
                $em->flush();

                return new JsonResponse(['removed' => [$preview->getId()]]);
            }
        );
    }
}
