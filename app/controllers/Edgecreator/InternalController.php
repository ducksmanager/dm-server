<?php

namespace DmServer\Controllers\Edgecreator;

use Dm\Models\Users;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Edgecreator\Models\EdgecreatorIntervalles;
use Edgecreator\Models\EdgecreatorModeles2;
use Edgecreator\Models\EdgecreatorValeurs;
use Edgecreator\Models\ImagesMyfonts;
use Edgecreator\Models\ImagesTranches;
use Edgecreator\Models\TranchesEnCoursContributeurs;
use Edgecreator\Models\TranchesEnCoursModeles;
use Edgecreator\Models\TranchesEnCoursModelesImages;
use Edgecreator\Models\TranchesEnCoursValeurs;
use Silex\Application;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;

/**
 * @SLX\Controller(prefix="/internal/edgecreator")
 */
class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_EDGECREATOR, $function);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="step/{publicationCode}/{stepNumber}"),
     *     @SLX\Assert(variable="publicationCode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *     @SLX\Assert(variable="stepNumber", regex="^(?P<stepnumber_regex>\-?\d+)$")
     * )
     * @param Request $request
     * @param Application $app
     * @param string $publicationCode
     * @param string $stepNumber
     * @return Response
     */
    public function createStepV1(Request $request, Application $app, $publicationCode, $stepNumber) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="v2/model/{publicationCode}/{issueNumber}/{isEditor}"),
     *     @SLX\Assert(variable="publicationCode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *     @SLX\Assert(variable="issueNumber", regex="^(?P<issuenumber_regex>[-A-Z0-9 ]+)$"),
     *     @SLX\Assert(variable="isEditor", regex="^(?P<iseditor_regex>0|1)$")
     * )
     * @param Application $app
     * @param string $publicationCode
     * @param string $issueNumber
     * @param string $isEditor
     * @return Response
     */
    public function createModelV2(Application $app, $publicationCode, $issueNumber, $isEditor) {
        return self::wrapInternalService($app, function(EntityManager $ecEm) use ($app, $publicationCode, $issueNumber, $isEditor) {
            list($country, $publication) = explode('/', $publicationCode);

            $model = new TranchesEnCoursModeles();
            $model->setPays($country);
            $model->setMagazine($publication);
            $model->setNumero($issueNumber);
            $model->setUsername($isEditor === '1' ? self::getSessionUser($app)['username'] : null);
            $model->setActive(true);

            $ecEm->persist($model);
            $ecEm->flush();

            return new JsonResponse(['modelid' => $model->getId()]);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="value")
     * )
     * @param Request $request
     * @param Application $app
     * @return Response
     */
    public function createValueV1(Request $request, Application $app) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="v2/step/{modelId}/{stepNumber}"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$"),
     *     @SLX\Assert(variable="stepNumber", regex="^(?P<stepnumber_regex>\-?\d+)$")
     * )
     * @param Request $request
     * @param Application $app
     * @param string $modelId
     * @param string $stepNumber
     * @return Response
     */
    public function createStepV2(Request $request, Application $app, $modelId, $stepNumber) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="interval/{valueId}/{firstIssueNumber}/{lastIssueNumber}"),
     * )
     * @param Application $app
     * @param string $valueId
     * @param string $firstIssueNumber
     * @param string $lastIssueNumber
     * @return Response
     */
    public function createV1Interval(Application $app, $valueId, $firstIssueNumber, $lastIssueNumber) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="v2/step/clone/{modelId}/{stepNumber}/{newStepNumber}"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$"),
     *     @SLX\Assert(variable="stepNumber", regex="^(?P<stepnumber_regex>\-?\d+)$"),
     *     @SLX\Assert(variable="newStepNumber", regex="^(?&stepnumber_regex)$")
     * )
     * @param Application $app
     * @param string $modelId
     * @param string $stepNumber
     * @param string $newStepNumber
     * @return Response
     */
    public function cloneStepV2(Application $app, $modelId, $stepNumber, $newStepNumber) {
        return self::wrapInternalService($app, function(EntityManager $ecEm) use ($modelId, $stepNumber, $newStepNumber) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="v2/step/shift/{modelId}/{stepNumber}/{isIncludingThisStep}"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$"),
     *     @SLX\Assert(variable="stepNumber", regex="^(?P<stepnumber_regex>\-?\d+)$"),
     *     @SLX\Assert(variable="isIncludingThisStep", regex="^(?P<isincludingthisstep_regex>(inclusive)|(exclusive))$")
     * )
     * @param Application $app
     * @param string $modelId
     * @param string $stepNumber
     * @param string $isIncludingThisStep
     * @return Response
     */
    public function shiftV2Step(Application $app, $modelId, $stepNumber, $isIncludingThisStep) {
        return self::wrapInternalService($app, function(EntityManager $ecEm) use ($modelId, $stepNumber, $isIncludingThisStep) {
            $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);

            $stepNumber = (int) $stepNumber;

            $criteria = new Criteria();
            $criteria
                ->where(Criteria::expr()->andX(
                    Criteria::expr()->eq('idModele', $model),
                    $isIncludingThisStep ==='inclusive'
                        ? Criteria::expr()->gte('ordre', $stepNumber)
                        : Criteria::expr()->gt ('ordre', $stepNumber)
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="DELETE", uri="v2/step/{modelId}/{stepNumber}"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$"),
     *     @SLX\Assert(variable="stepNumber", regex="^(?P<stepnumber_regex>\-?\d+)$")
     * )
     * @param Application $app
     * @param string $modelId
     * @param string $stepNumber
     * @return Response
     */
    public function deleteV2Step(Application $app, $modelId, $stepNumber) {
        return self::wrapInternalService($app, function(EntityManager $ecEm) use ($modelId, $stepNumber) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="v2/model"),
     * )
     * @param Application $app
     * @return Response
     */
    public function getV2MyModels(Application $app) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($app) {
            $qb = $ecEm->createQueryBuilder();

            $qb->select('modeles.id, modeles.pays, modeles.magazine, modeles.numero, image.nomfichier, modeles.username,'
                .' (case when modeles.username = :username then 1 else 0 end) as est_editeur')
                ->from(TranchesEnCoursModeles::class, 'modeles')
                ->leftJoin('modeles.contributeurs', 'helperusers')
                ->leftJoin('modeles.photos', 'photos')
                ->leftJoin('photos.image', 'image')
                ->andWhere("modeles.active = :active")
                ->setParameter(':active', true)
                ->andWhere("modeles.username = :username or helperusers.idUtilisateur = :usernameid")
                ->setParameter(':username', self::getSessionUser($app)['username'])
                ->setParameter(':usernameid', self::getSessionUser($app)['id'])
            ;

            $models = $qb->getQuery()->getResult();

            return new JsonResponse(self::getSerializer()->serialize($models, 'json'), Response::HTTP_OK, [], true);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="v2/model/{modelId}"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param string $modelId
     * @return Response
     */
    public function getV2ModelById(Application $app, $modelId) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($modelId) {
            $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
            return new JsonResponse(self::getSerializer()->serialize($model, 'json'), Response::HTTP_OK, [], true);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="v2/model/editedbyother/all")
     * )
     * @param Application $app
     * @return Response
     */
    public function getV2ModelsPhotoByMeEditedByOthers(Application $app) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($app) {
            $qb = $ecEm->createQueryBuilder();

            $qb->select('modeles.id, modeles.pays, modeles.magazine, modeles.numero')
                ->from(TranchesEnCoursModeles::class, 'modeles')
                ->leftJoin('modeles.contributeurs', 'helperusers')
                ->andWhere("modeles.active = :active")
                ->setParameter(':active', true)
                ->andWhere("modeles.username != :username or modeles.username is null")
                ->andWhere("helperusers.idUtilisateur = :usernameid and helperusers.contribution = :contribution")
                ->setParameter(':username', self::getSessionUser($app)['username'])
                ->setParameter(':usernameid', self::getSessionUser($app)['id'])
                ->setParameter(':contribution', 'photographe')
            ;

            $models = $qb->getQuery()->getResult();

            return new JsonResponse(self::getSerializer()->serialize($models, 'json'), Response::HTTP_OK, [], true);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="v2/model/unassigned/all")
     * )
     * @param Application $app
     * @return Response
     */
    public function getV2UnassignedModels(Application $app) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) {
            $models = $ecEm->getRepository(TranchesEnCoursModeles::class)->findBy([
                'username' => null
            ]);

            return new JsonResponse(self::getSerializer()->serialize($models, 'json'), Response::HTTP_OK, [], true);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="v2/model/{publicationCode}/{issueNumber}"),
     *     @SLX\Assert(variable="publicationCode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *     @SLX\Assert(variable="issueNumber", regex="^(?P<issuenumber_regex>[-A-Z0-9 ]+)$")
     * )
     * @param Application $app
     * @param string $publicationCode
     * @param string $issueNumber
     * @return Response
     */
    public function getV2ModelByPublicationCodeAndIssueNumber(Application $app, $publicationCode, $issueNumber) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($publicationCode, $issueNumber) {
            list($country, $magazine) = explode('/', $publicationCode);
            $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->findOneBy([
                'pays' => $country,
                'magazine' => $magazine,
                'numero' => $issueNumber
            ]);

            if (is_null($model)) {
                return new Response('', Response::HTTP_NO_CONTENT);
            }
            else {
                return new JsonResponse(self::getSerializer()->serialize($model, 'json'), Response::HTTP_OK, [], true);
            }
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="myfontspreview")
     * )
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function storeMyFontsPreview(Application $app, Request $request) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="DELETE", uri="myfontspreview/{previewId}")
     * )
     * @param Application $app
     * @param string $previewId
     * @return Response
     */
    public function deleteMyFontsPreview(Application $app, $previewId) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($previewId) {
            $preview = $ecEm->getRepository(ImagesMyfonts::class)->find($previewId);
            $ecEm->remove($preview);
            $ecEm->flush();

            return new JsonResponse(['removed' => [$preview->getId()]]);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="model/v2/{modelId}/deactivate"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param string $modelId
     * @return Response
     */
    public function deactivateV2Model(Application $app, $modelId) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($modelId) {
            $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
            $model->setActive(false);
            $ecEm->persist($model);
            $ecEm->flush();

            return new JsonResponse(['deactivated' => $model->getId()]);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="v2/model/{modelId}/empty"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param string $modelId
     * @return Response
     * @internal param Request $request
     */
    public function deleteSteps(Application $app, $modelId) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($modelId) {
            $qbDeleteSteps = $ecEm->getRepository(TranchesEnCoursModeles::class)->createQueryBuilder($modelId);
            $qbDeleteSteps
                ->delete(TranchesEnCoursValeurs::class, 'values')
                ->where('values.idModele = :modelid')
                ->setParameter('modelid', $modelId);

            $deletedSteps = $qbDeleteSteps->getQuery()->execute();

            return new JsonResponse([
                'steps' => ['deleted' => $deletedSteps]
            ]);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="model/v2/{modelId}/readytopublish/{isReadyToPublish}"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param Request $request
     * @param string $modelId
     * @param string $isReadyToPublish
     * @return Response
     */
    public function setModelAsReadyToBePublished(Application $app, Request $request, $modelId, $isReadyToPublish) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($request, $modelId, $isReadyToPublish) {
            $designers = $request->request->get('designers');
            $photographers = $request->request->get('photographers');

            $dmEm = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM);

            $qb = $dmEm->createQueryBuilder();
            $qb->select('users.id, users.username')
                ->from(Users::class, 'users')
                ->andWhere("users.username in (:usernames)")
                ->setParameter(':usernames', $designers+$photographers)
            ;

            $contributorsIdsResults = $qb->getQuery()->getResult();

            $contributorsIds = [];
            array_walk($contributorsIdsResults, function($value) use (&$contributorsIds) {
                $contributorsIds[$value['username']] = $value['id'];
            });

            /** @var TranchesEnCoursModeles $model */
            $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);

            $contributors = [];
            if (!is_null($photographers)) {
                $contributors = array_merge($contributors, array_map(function($photographUsername) use ($contributorsIds, $model) {
                    $photographer = new TranchesEnCoursContributeurs();
                    $photographer->setContribution('photographe');
                    $photographer->setIdUtilisateur($contributorsIds[$photographUsername]);
                    $photographer->setModele($model);

                    return $photographer;
                }, $photographers));
            }
            if (!is_null($designers)) {
                $contributors = array_merge($contributors, array_map(function($designorUsername) use ($contributorsIds, $model) {
                    $designer = new TranchesEnCoursContributeurs();
                    $designer->setContribution('createur');
                    $designer->setIdUtilisateur($contributorsIds[$designorUsername]);
                    $designer->setModele($model);

                    return $designer;
                }, $designers));
            }
            $model->setActive(false);
            $model->setPretepourpublication($isReadyToPublish === '1');

            if (!is_null($photographers) || !is_null($designers)) {
                $model->setContributeurs($contributors);
            }

            $ecEm->persist($model);
            $ecEm->flush();

            return new JsonResponse(self::getSerializer()->serialize([
                'model' => $model,
                'readytopublish' => $isReadyToPublish === '1'
            ], 'json'), 200, [], true);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="model/v2/{modelId}/photo/main"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param Request $request
     * @param string $modelId
     * @return Response
     */
    public function setModelMainPhoto(Application $app, Request $request, $modelId) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($app, $request, $modelId) {
            $photoName = $request->request->get('photoname');

            /** @var TranchesEnCoursModeles $model */
            $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);

            /** @var Collection $helperUsers */
            $helperUsers = $model->getContributeurs();

            $photographer = new TranchesEnCoursContributeurs();
            $photographer->setModele($model);
            $photographer->setContribution('photographe');
            $photographer->setIdUtilisateur(self::getSessionUser($app)['id']);
            $model->setContributeurs(array_merge($helperUsers->toArray(), [$photographer]));
            $ecEm->persist($model);

            $mainPhoto = new ImagesTranches();
            $mainPhoto
                ->setIdUtilisateur(self::getSessionUser($app)['id'])
                ->setDateheure((new \DateTime())->setTime(0,0))
                ->setHash(null) // TODO
                ->setNomfichier($photoName);
            $ecEm->persist($mainPhoto);

            $photoAndEdge = new TranchesEnCoursModelesImages();
            $photoAndEdge
                ->setImage($mainPhoto)
                ->setModele($model)
                ->setEstphotoprincipale(true);
            $ecEm->persist($photoAndEdge);

            $ecEm->flush();

            return new JsonResponse(['mainphoto' => ['modelid' => $model->getId(), 'photoname' => $mainPhoto->getNomfichier()]]);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="model/v2/{modelId}/photo/main"),
     *     @SLX\Assert(variable="modelId", regex="^(?P<modelid_regex>\d+)$")
     * )
     * @param Application $app
     * @param string $modelId
     * @return Response
     */
    public function getModelMainPhoto(Application $app, $modelId) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($modelId) {
            $qb = $ecEm->createQueryBuilder();

            $qb->select('photo.id, photo.nomfichier')
                ->from(TranchesEnCoursModelesImages::class, 'modelsPhotos')
                ->innerJoin('modelsPhotos.modele', 'model')
                ->innerJoin('modelsPhotos.image', 'photo')
                ->andWhere("model.id = :modelId")
                ->setParameter(':modelId', $modelId)
                ->andWhere("modelsPhotos.estphotoprincipale = 1");

            try {
                $mainPhoto = $qb->getQuery()->getSingleResult();
            }
            catch(NoResultException $e) {
                return new Response('', Response::HTTP_NO_CONTENT);
            }

            return new JsonResponse(self::getSerializer()->serialize($mainPhoto, 'json'), Response::HTTP_OK, [], true);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="multiple_edge_photo/today")
     * )
     * @param Application $app
     * @return Response
     */
    public function getMultipleEdgePhotosFromToday(Application $app) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($app) {
            $qb = $ecEm->createQueryBuilder();

            $qb->select('photo.id, photo.hash, photo.dateheure, photo.idUtilisateur')
                ->from(ImagesTranches::class, 'photo')
                ->andWhere("photo.idUtilisateur = :idUtilisateur")
                ->setParameter(':idUtilisateur', self::getSessionUser($app)['id'])
                ->andWhere("photo.dateheure = :today")
                ->setParameter(':today', new \DateTime('today'));

            $uploadedFiles = $qb->getQuery()->getResult();

            return new JsonResponse(self::getSerializer()->serialize($uploadedFiles, 'json'), Response::HTTP_OK, [], true);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="multiple_edge_photo/{hash}")
     * )
     * @param Application $app
     * @param string $hash
     * @return Response
     */
    public function getMultipleEdgePhotoFromHash(Application $app, $hash) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($app, $hash) {
            $uploadedFile = $ecEm->getRepository(ImagesTranches::class)->findOneBy([
                'idUtilisateur' => self::getSessionUser($app)['id'],
                'hash' => $hash
            ]);
            return new JsonResponse(self::getSerializer()->serialize($uploadedFile, 'json'), Response::HTTP_OK, [], true);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="multiple_edge_photo")
     * )
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function createMultipleEdgePhoto(Application $app, Request $request) {
        return self::wrapInternalService($app, function (EntityManager $ecEm) use ($request, $app) {
            $hash = $request->request->get('hash');
            $fileName = $request->request->get('filename');
            $user = self::getSessionUser($app);
            /** @var Swift_Mailer $mailer */
            $mailer = $app['mailer'];

            $photo = new ImagesTranches();
            $photo->setHash($hash);
            $photo->setDateheure(new \DateTime('today'));
            $photo->setNomfichier($fileName);
            $photo->setIdUtilisateur($user['id']);
            $ecEm->persist($photo);
            $ecEm->flush();

            $message = new \Swift_Message();
            $message
                ->setSubject('Nouvelle photo de tranche')
                ->setFrom([$user['username']."@".DmServer::$settings['smtp_origin_email_domain_edgecreator']])
                ->setTo([DmServer::$settings['smtp_username']])
                ->setBody($fileName);

            $failures = [];
            // Pass a variable name to the send() method
            if (!$mailer->send($message, $failures)) {
                throw new \Exception("Can't send e-mail '$message': failed with ".print_r($failures, true));
            }

            return new JsonResponse(['photo' => ['id' => $photo->getId()]]);
        });
    }
}
