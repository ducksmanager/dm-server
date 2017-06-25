<?php

namespace DmServer\Controllers\EdgeCreator;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
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
use Symfony\Component\HttpFoundation\Response;

class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::return500ErrorOnException($app, DmServer::CONFIG_DB_KEY_EDGECREATOR, $function);
    }
    
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->put(
            '/internal/edgecreator/step/{publicationCode}/{stepNumber}',
            function (Request $request, Application $app, $publicationCode, $stepNumber) {
                return self::wrapInternalService($app, function(EntityManager $ecEm) use ($request, $publicationCode, $stepNumber) {
                    list($country, $publication) = explode('/', $publicationCode);
                    $functionName = $request->request->get('functionname');
                    $optionName = $request->request->get('optionname');

                    $model = new EdgecreatorModeles2();
                    $model->setPays($country);
                    $model->setMagazine($publication);
                    $model->setOrdre((int) $stepNumber);
                    $model->setNomFonction($functionName);
                    $model->setOptionNom($optionName);

                    $ecEm->persist($model);
                    $ecEm->flush();

                    return new JsonResponse(['optionid' => $model->getId()]);
                });
            }
        )
            ->assert('publicationCode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('stepNumber', self::getParamAssertRegex('[-\\d]+'));

        $routing->put(
            '/internal/edgecreator/v2/model/{publicationCode}/{issueNumber}',
            function (Request $request, Application $app, $publicationCode, $issueNumber) {
                return self::wrapInternalService($app, function(EntityManager $ecEm) use ($app, $publicationCode, $issueNumber) {
                    list($country, $publication) = explode('/', $publicationCode);

                    $model = new TranchesEnCoursModeles();
                    $model->setPays($country);
                    $model->setMagazine($publication);
                    $model->setNumero($issueNumber);
                    $model->setUsername(self::getSessionUser($app)['username']);
                    $model->setActive(true);

                    $ecEm->persist($model);
                    $ecEm->flush();

                    return new JsonResponse(['modelid' => $model->getId()]);
                });
            }
        )
            ->assert('publicationCode', self::getParamAssertRegex(\Coa\Models\BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('issueNumber', self::getParamAssertRegex(\Coa\Models\BaseModel::ISSUE_NUMBER_VALIDATION))
        ;

        $routing->put(
            '/internal/edgecreator/value',
            function (Request $request, Application $app) {
                return self::wrapInternalService($app, function(EntityManager $ecEm) use ($request) {
                    $optionId = $request->request->get('optionid');
                    $optionValue = $request->request->get('optionvalue');

                    $value = new EdgecreatorValeurs();
                    $value->setIdOption($optionId);
                    $value->setOptionValeur($optionValue);

                    $ecEm->persist($value);
                    $ecEm->flush();

                    return new JsonResponse(['valueid' => $value->getId()]);
                });
            }
        );

        $routing->put(
            '/internal/edgecreator/v2/model/{modelId}/{stepNumber}',
            function (Request $request, Application $app, $modelId, $stepNumber) {
                return self::wrapInternalService($app, function(EntityManager $ecEm) use ($request, $modelId, $stepNumber) {
                    $qb = $ecEm->createQueryBuilder();

                    $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                    $options = $request->request->get('options');
                    $newFunctionName = $request->request->get('newFunctionName');

                    if (is_null($options)) {
                        throw new \Exception('No options provided, ignoring step '.$stepNumber);
                    }
                    if (!is_array($options)) {
                        throw new \Exception('Invalid options input : '.print_r($options, true));
                    }

                    if (is_null($newFunctionName)) {
                        /** @var TranchesEnCoursValeurs $existingValue */
                        $existingValue = $ecEm->getRepository(TranchesEnCoursValeurs::class)->findOneBy([
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

                    array_walk($options, function($optionValue, $optionName) use ($ecEm, $model, $stepNumber, $newFunctionName, &$createdOptions) {
                        $optionToCreate = new TranchesEnCoursValeurs();
                        $optionToCreate->setIdModele($model);
                        $optionToCreate->setOrdre((int)$stepNumber);
                        $optionToCreate->setNomFonction($newFunctionName);
                        $optionToCreate->setOptionNom($optionName);
                        $optionToCreate->setOptionValeur($optionValue);

                        $ecEm->persist($optionToCreate);
                        $createdOptions[] = ['name' => $optionName, 'value' => $optionValue];
                    });

                    $ecEm->flush();
                    $ecEm->clear();

                    return new JsonResponse(['valueids' => $createdOptions]);
                });
            }
        )
            ->assert('modelId', self::getParamAssertRegex('\\d+'))
            ->assert('stepNumber', self::getParamAssertRegex('[-\\d]+'));

        $routing->put(
            '/internal/edgecreator/interval/{valueId}/{firstIssueNumber}/{lastIssueNumber}',
            function (Request $request, Application $app, $valueId, $firstIssueNumber, $lastIssueNumber) {
                return self::wrapInternalService($app, function(EntityManager $ecEm) use ($app, $valueId, $firstIssueNumber, $lastIssueNumber) {
                    $interval = new EdgecreatorIntervalles();

                    $interval->setIdValeur($valueId);
                    $interval->setNumeroDebut($firstIssueNumber);
                    $interval->setNumeroFin($lastIssueNumber);
                    $interval->setUsername(self::getSessionUser($app)['username']);

                    $ecEm->persist($interval);
                    $ecEm->flush();

                    return new JsonResponse(['intervalid' => $interval->getId()]);
                });
            }
        );

        $routing->post(
            '/internal/edgecreator/step/clone/{modelId}/{stepNumber}/{newStepNumber}',
            function (Request $request, Application $app, $modelId, $stepNumber, $newStepNumber) {
                return self::wrapInternalService($app, function(EntityManager $ecEm) use ($app, $modelId, $stepNumber, $newStepNumber) {
                    $criteria = [
                        'idModele' => $modelId,
                        'ordre' => $stepNumber
                    ];
                    /** @var TranchesEnCoursValeurs[] $values */
                    $values = $ecEm->getRepository(TranchesEnCoursValeurs::class)->findBy($criteria);

                    if (count($values) === 0) {
                        throw new \Exception('No values to clone for '.json_encode($criteria, true));
                    }

                    $functionName = $values[0]->getNomFonction();

                    $newStepNumbers = array_map(function(TranchesEnCoursValeurs $value) use ($ecEm, $newStepNumber) {
                        $oldStepNumber = $value->getOrdre();
                        $newValue = new TranchesEnCoursValeurs();
                        $newValue->setIdModele($value->getIdModele());
                        $newValue->setNomFonction($value->getNomFonction());
                        $newValue->setOptionNom($value->getOptionNom());
                        $newValue->setOptionValeur($value->getOptionValeur());
                        $newValue->setOrdre((int)$newStepNumber);
                        $ecEm->persist($newValue);

                        return [['old' => $oldStepNumber, 'new' => $newValue->getOrdre()]];
                    }, $values);

                    $uniqueStepChanges = array_values(array_unique($newStepNumbers, SORT_REGULAR ));

                    $ecEm->flush();

                    return new JsonResponse(['newStepNumbers' => array_unique($uniqueStepChanges), 'functionName' => $functionName]);
                });
            }
        )
            ->assert('stepNumber', self::getParamAssertRegex('[-\\d]+'))
            ->assert('newStepNumber', self::getParamAssertRegex('\\d+'));

        $routing->post(
            '/internal/edgecreator/step/shift/{modelId}/{stepNumber}/{isIncludingThisStep}',
            function (Request $request, Application $app, $modelId, $stepNumber, $isIncludingThisStep) {
                return self::wrapInternalService($app, function(EntityManager $ecEm) use ($app, $modelId, $stepNumber, $isIncludingThisStep) {
                    $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);

                    $stepNumber = (int) $stepNumber;

                    $criteria = new Criteria();
                    $criteria
                        ->where($criteria->expr()->andX(
                            $criteria->expr()->eq('idModele', $model),
                            $isIncludingThisStep ==='inclusive'
                                ? $criteria->expr()->gte('ordre', $stepNumber)
                                : $criteria->expr()->gt ('ordre', $stepNumber)
                        ));

                    $values = $ecEm->getRepository(TranchesEnCoursValeurs::class)->matching($criteria);

                    $shifts = array_map(
                        function(TranchesEnCoursValeurs $value) use ($ecEm) {
                            $shift = ['old' => $value->getOrdre(), 'new' => $value->getOrdre() + 1];
                            $value->setOrdre($value->getOrdre() + 1);
                            $ecEm->persist($value);

                            return $shift;
                    }, $values->toArray());

                    $uniqueStepShifts = array_values(array_unique($shifts, SORT_REGULAR ));

                    $ecEm->flush();

                    return new JsonResponse(['shifts' => $uniqueStepShifts ]);
                });
            }
        )
            ->assert('stepNumber', self::getParamAssertRegex('[-\\d]+'));


        $routing->delete(
            '/internal/edgecreator/step/{modelId}/{stepNumber}',
            function (Request $request, Application $app, $modelId, $stepNumber) {
                return self::wrapInternalService($app, function(EntityManager $ecEm) use ($app, $modelId, $stepNumber) {
                    $qb = $ecEm->createQueryBuilder();

                    $qb->delete(TranchesEnCoursValeurs::class, 'values')
                        ->andWhere($qb->expr()->eq('values.idModele', ':modelId'))
                        ->setParameter(':modelId', $modelId)
                        ->andWhere($qb->expr()->eq('values.ordre', ':stepNumber'))
                        ->setParameter(':stepNumber', $stepNumber);
                    $qb->getQuery()->execute();

                    $ecEm->flush();

                    return new JsonResponse(['removed' => ['model' => $modelId, 'step' => $stepNumber ]]);
                });
            }
        )
            ->assert('stepNumber', self::getParamAssertRegex('[-\\d]+'));

        $routing->get(
            '/internal/edgecreator/v2/model/{modelId}',
            function (Request $request, Application $app, $modelId) {
                return self::wrapInternalService($app, function (EntityManager $ecEm) use ($app, $modelId) {
                    $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                    return new JsonResponse(self::getSerializer()->serialize($model, 'json'), Response::HTTP_OK, [], true);
                });
            }
        );

        $routing->put(
            '/internal/edgecreator/myfontspreview',
            function (Application $app, Request $request) {
                return self::wrapInternalService($app, function (EntityManager $ecEm) use ($request) {
                    $preview = new ImagesMyfonts();

                    $preview->setFont($request->request->get('font'));
                    $preview->setColor($request->request->get('fgColor'));
                    $preview->setColorbg($request->request->get('bgColor'));
                    $preview->setWidth($request->request->get('width'));
                    $preview->setTexte($request->request->get('text'));
                    $preview->setPrecision($request->request->get('precision'));

                    $ecEm->persist($preview);
                    $ecEm->flush();

                    return new JsonResponse(['previewid' => $preview->getId()]);
                });
            }
        );

        $routing->delete(
            '/internal/edgecreator/myfontspreview/{previewId}',
            function (Application $app, Request $request, $previewId) {
                return self::wrapInternalService($app, function (EntityManager $ecEm) use ($previewId) {
                    $preview = $ecEm->getRepository(ImagesMyfonts::class)->find($previewId);
                    $ecEm->remove($preview);
                    $ecEm->flush();

                    return new JsonResponse(['removed' => [$preview->getId()]]);
                });
            }
        );

        $routing->post(
            '/internal/edgecreator/model/v2/{modelId}/deactivate',
            function (Application $app, Request $request, $modelId) {
                return self::wrapInternalService($app, function (EntityManager $ecEm) use ($modelId) {
                    $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                    $model->setActive(false);
                    $ecEm->persist($model);
                    $ecEm->flush();

                    return new JsonResponse(['deactivated' => $model->getId()]);
                });
            }
        );

        $routing->post(
            '/internal/edgecreator/model/v2/{modelId}/readytopublish/{isReadyToPublish}',
            function (Application $app, Request $request, $modelId, $isReadyToPublish) {
                return self::wrapInternalService($app, function (EntityManager $ecEm) use ($request, $modelId, $isReadyToPublish) {
                    $designers = $request->request->get('designers');
                    $photographers = $request->request->get('photographers');

                    /** @var TranchesEnCoursModeles $model */
                    $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                    $model->setActive(false);
                    if (!is_null($photographers)) {
                        $model->setPhotographes(implode(',', $photographers));
                    }
                    if (!is_null($designers)) {
                        $model->setCreateurs(implode(',', $designers));
                    }
                    $model->setPretepourpublication($isReadyToPublish === '1');
                    $ecEm->persist($model);
                    $ecEm->flush();

                    return new JsonResponse(self::getSerializer()->serialize([
                        'model' => $model,
                        'readytopublish' => $isReadyToPublish === '1'
                    ], 'json'), 200, [], true);
                });
            }
        );

        $routing->put(
            '/internal/edgecreator/model/v2/{modelId}/photo/main',
            function (Application $app, Request $request, $modelId) {
                return self::wrapInternalService($app, function (EntityManager $ecEm) use ($request, $modelId) {
                    $photoName = $request->request->get('photoname');

                    $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
                    $model->setNomphotoprincipale($photoName);
                    $ecEm->persist($model);
                    $ecEm->flush();

                    return new JsonResponse(['mainphoto' => ['modelid' => $model->getId(), 'photoname' => $photoName]]);
                });
            }
        );
    }
}
