<?php

namespace DmServer\Controllers\EdgeCreator;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
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
            '/internal/edgecreator/v2/model/{modelId}/{stepNumber}',
            function (Request $request, Application $app, $modelId, $stepNumber) {
                return AbstractController::return500ErrorOnException($app, function() use ($request, $modelId, $stepNumber) {
                    $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);
                    $qb = $em->createQueryBuilder();

                    $model = $em->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                    $options = $request->request->get('options');
                    $newFunctionName = $request->request->get('newFunctionName');

                    if (is_null($options) || !is_array($options) ) {
                        throw new \Exception('Invalid options input');
                    }

                    if (is_null($newFunctionName)) {
                        /** @var TranchesEnCoursValeurs $existingValue */
                        $existingValue = $em->getRepository(TranchesEnCoursValeurs::class)->findOneBy([
                            'idModele' => $modelId,
                            'ordre' => $stepNumber
                        ]);

                        if (is_null($existingValue)) {
                            throw new \Exception('No option exists for this step and no function name was provided');
                        }
                        $newFunctionName = $existingValue->getNomFonction();
                    }

                    $qb
                        ->delete(TranchesEnCoursValeurs::class, 'values')

                        ->andWhere($qb->expr()->eq('values.idModele', ':modelId'))
                        ->setParameter(':modelId', $modelId)

                        ->andWhere($qb->expr()->eq('values.ordre', ':stepNumber'))
                        ->setParameter(':stepNumber', $stepNumber);

                    $qb->getQuery()->getResult();

                    $createdOptions = [];

                    array_walk($options, function($optionValue, $optionName) use ($em, $model, $stepNumber, $newFunctionName, &$createdOptions) {
                        $optionToCreate = new TranchesEnCoursValeurs();
                        $optionToCreate->setIdModele($model);
                        $optionToCreate->setOrdre((int)$stepNumber);
                        $optionToCreate->setNomFonction($newFunctionName);
                        $optionToCreate->setOptionNom($optionName);
                        $optionToCreate->setOptionValeur($optionValue);

                        $em->persist($optionToCreate);
                        $createdOptions[] = ['name' => $optionName, 'value' => $optionValue];
                    });

                    $em->flush();
                    $em->clear();

                    return new JsonResponse(['valueids' => $createdOptions]);
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
                    $interval->setUsername(self::getSessionUser($app)['username']);

                    $em->persist($interval);
                    $em->flush();

                    return new JsonResponse(['intervalid' => $interval->getId()]);
                });
            }
        );

        $routing->post(
            '/internal/edgecreator/step/clone/{modelId}/{stepNumber}/{newStepNumber}',
            function (Request $request, Application $app, $modelId, $stepNumber, $newStepNumber) {
                return AbstractController::return500ErrorOnException($app, function() use ($app, $modelId, $stepNumber, $newStepNumber) {
                    $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                    $criteria = [
                        'idModele' => $modelId,
                        'ordre' => $stepNumber
                    ];
                    /** @var TranchesEnCoursValeurs[] $values */
                    $values = $em->getRepository(TranchesEnCoursValeurs::class)->findBy($criteria);

                    if (count($values) === 0) {
                        throw new \Exception('No values to clone for '.json_encode($criteria, true));
                    }

                    $functionName = $values[0]->getNomFonction();

                    $newStepNumbers = array_map(function(TranchesEnCoursValeurs $value) use ($em, $newStepNumber) {
                        $oldStepNumber = $value->getOrdre();
                        $newValue = new TranchesEnCoursValeurs();
                        $newValue->setIdModele($value->getIdModele());
                        $newValue->setNomFonction($value->getNomFonction());
                        $newValue->setOptionNom($value->getOptionNom());
                        $newValue->setOptionValeur($value->getOptionValeur());
                        $newValue->setOrdre((int)$newStepNumber);
                        $em->persist($newValue);

                        return [['old' => $oldStepNumber, 'new' => $newValue->getOrdre()]];
                    }, $values);

                    $uniqueStepChanges = array_values(array_unique($newStepNumbers, SORT_REGULAR ));

                    $em->flush();

                    return new JsonResponse(['newStepNumbers' => array_unique($uniqueStepChanges), 'functionName' => $functionName]);
                });
            }
        )
            ->assert('stepNumber', self::getParamAssertRegex('\\d+'))
            ->assert('newStepNumber', self::getParamAssertRegex('\\d+'));

        $routing->post(
            '/internal/edgecreator/step/shift/{modelId}/{stepNumber}/{isIncludingThisStep}',
            function (Request $request, Application $app, $modelId, $stepNumber, $isIncludingThisStep) {
                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                $model = $em->getRepository(TranchesEnCoursModeles::class)->find($modelId);

                $stepNumber = (int) $stepNumber;

                $criteria = new Criteria();
                $criteria
                    ->where($criteria->expr()->andX(
                        $criteria->expr()->eq('idModele', $model),
                        $isIncludingThisStep ==='inclusive'
                            ? $criteria->expr()->gte('ordre', $stepNumber)
                            : $criteria->expr()->gt ('ordre', $stepNumber)
                    ));

                $values = $em->getRepository(TranchesEnCoursValeurs::class)->matching($criteria);

                $shifts = array_map(
                    function(TranchesEnCoursValeurs $value) use ($em) {
                        $shift = ['old' => $value->getOrdre(), 'new' => $value->getOrdre() + 1];
                        $value->setOrdre($value->getOrdre() + 1);
                        $em->persist($value);

                        return $shift;
                }, $values->toArray());

                $uniqueStepShifts = array_values(array_unique($shifts, SORT_REGULAR ));

                $em->flush();

                return new JsonResponse(['shifts' => $uniqueStepShifts ]);
            }
        )
            ->assert('stepNumber', self::getParamAssertRegex('\\d+'));


        $routing->delete(
            '/internal/edgecreator/step/{modelId}/{stepNumber}',
            function (Request $request, Application $app, $modelId, $stepNumber) {
                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);
                $qb = $em->createQueryBuilder();

                $qb->delete(TranchesEnCoursValeurs::class, 'values')
                    ->andWhere($qb->expr()->eq('values.idModele', ':modelId'))
                    ->setParameter(':modelId', $modelId)
                    ->andWhere($qb->expr()->eq('values.ordre', ':stepNumber'))
                    ->setParameter(':stepNumber', $stepNumber);
                $qb->getQuery()->execute();

                $em->flush();

                return new JsonResponse(['removed' => ['model' => $modelId, 'step' => $stepNumber ]]);
            }
        )
            ->assert('stepNumber', self::getParamAssertRegex('\\d+'));

        $routing->get(
            '/internal/edgecreator/v2/model/{modelId}',
            function (Request $request, Application $app, $modelId) {
                return AbstractController::return500ErrorOnException($app, function () use ($app, $modelId) {
                    $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);
                    $model = $em->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                    return new JsonResponse(self::getSerializer()->serialize($model, 'json'));
                });
            }
        );

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

        $routing->post(
            '/internal/edgecreator/model/v2/{modelId}/deactivate',
            function (Application $app, Request $request, $modelId) {
                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                $model = $em->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                $model->setActive(false);
                $em->persist($model);
                $em->flush();

                return new JsonResponse(['deactivated' => $model->getId()]);
            }
        );

        $routing->post(
            '/internal/edgecreator/model/v2/{modelId}/readytopublish/{isReadyToPublish}',
            function (Application $app, Request $request, $modelId, $isReadyToPublish) {
                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                $model = $em->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                $model->setActive(false);
                $model->setPretepourpublication($isReadyToPublish === '1');
                $em->persist($model);
                $em->flush();

                return new JsonResponse(['readytopublish' => ['modelid' => $model->getId(), 'readytopublish' => $isReadyToPublish === '1']]);
            }
        );

        $routing->put(
            '/internal/edgecreator/model/v2/{modelId}/photo/main',
            function (Application $app, Request $request, $modelId) {
                $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

                $photoName = $request->request->get('photoname');

                $model = $em->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                $model->setNomphotoprincipale($photoName);
                $em->persist($model);
                $em->flush();

                return new JsonResponse(['mainphoto' => ['modelid' => $model->getId(), 'photoname' => $photoName]]);
            }
        );
    }
}
