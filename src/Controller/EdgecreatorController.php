<?php

namespace App\Controller;

use App\Entity\Dm\NumerosPopularite;
use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersContributions;
use App\Entity\EdgeCreator\EdgecreatorIntervalles;
use App\Entity\EdgeCreator\EdgecreatorModeles2;
use App\Entity\EdgeCreator\EdgecreatorValeurs;
use App\Entity\EdgeCreator\ImagesMyfonts;
use App\Entity\EdgeCreator\ImagesTranches;
use App\Entity\EdgeCreator\TranchesEnCoursContributeurs;
use App\Entity\EdgeCreator\TranchesEnCoursModeles;
use App\Entity\EdgeCreator\TranchesEnCoursModelesImages;
use App\Entity\EdgeCreator\TranchesEnCoursValeurs;
use App\Helper\JsonResponseFromObject;
use App\Service\ContributionService;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EdgecreatorController extends AbstractController implements RequiresDmVersionController, RequiresDmUserController
{

    /**
     * @Route(
     *     methods={"PUT"},
     *     path="/edgecreator/step/{publicationCode}/{stepNumber}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"})
     */
    public function addStep (Request $request, string $publicationCode, string $stepNumber): JsonResponse
    {
        $functionName = $request->request->get('functionname');
        $optionName = $request->request->get('optionname');
        $optionValue = $request->request->get('optionvalue');
        $firstIssueNumber = $request->request->get('firstissuenumber');
        $lastIssueNumber = $request->request->get('lastissuenumber');

        $optionId = $this->createStepV1($publicationCode, $stepNumber, $functionName, $optionName);
        $valueId = $this->createValueV1($optionId, $optionValue);
        $intervalId = $this->createIntervalV1($valueId, $firstIssueNumber, $lastIssueNumber);

        return new JsonResponse(['optionid' => $optionId, 'valueid' => $valueId, 'intervalid' => $intervalId]);
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/v2/model")
     */
    public function getV2MyModels(): JsonResponse
    {
        $ecEm = $this->getEm('edgecreator');
        $qb = $ecEm->createQueryBuilder();

        $qb->select('modeles.id, modeles.pays, modeles.magazine, modeles.numero, image.nomfichier, modeles.username,'
            .' (case when modeles.username = :username then 1 else 0 end) as est_editeur')
            ->from(TranchesEnCoursModeles::class, 'modeles')
            ->leftJoin('modeles.contributeurs', 'helperusers')
            ->leftJoin('modeles.photos', 'photos')
            ->leftJoin('photos.idImage', 'image')
            ->andWhere('modeles.active = :active')
            ->setParameter(':active', true)
            ->andWhere('modeles.username = :username or helperusers.idUtilisateur = :usernameid')
            ->setParameter(':username', $this->getSessionUser()['username'])
            ->setParameter(':usernameid', $this->getSessionUser()['id'])
        ;

        return new JsonResponseFromObject($qb->getQuery()->getResult());
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/v2/model/{modelId}")
     */
    public function getModel(int $modelId): JsonResponse
    {
        return new JsonResponseFromObject(
            $this->getEm('edgecreator')->getRepository(TranchesEnCoursModeles::class)->find($modelId)
        );
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/v2/model/{modelId}/steps")
     */
    public function getSteps(int $modelId): JsonResponse
    {
        $ecEm = $this->getEm('edgecreator');
        $qb = $ecEm->createQueryBuilder();

        $options = <<<'CONCAT'
concat('{', group_concat(concat('"', values.optionNom, '": ', '"', values.optionValeur, '"')), '}')
CONCAT;
        $qb->select("values.ordre, values.nomFonction, $options AS options")
            ->from(TranchesEnCoursValeurs::class, 'values')
            ->andWhere('values.idModele = :modelId')
            ->setParameter(':modelId', $modelId)
            ->groupBy('values.ordre')
            ->orderBy('values.ordre')
        ;

        return new JsonResponse(array_map(function(array $result) {
            $result['options'] = json_decode($result['options'], true);
            return $result;
        }, $qb->getQuery()->getArrayResult()));
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/v2/model/editedbyother/all")
     */
    public function getModelsEditedByOthers(): JsonResponse
    {
        $qb = $this->getEm('edgecreator')->createQueryBuilder();

        $qb->select('modeles.id, modeles.pays, modeles.magazine, modeles.numero')
            ->from(TranchesEnCoursModeles::class, 'modeles')
            ->leftJoin('modeles.contributeurs', 'helperusers')
            ->andWhere('modeles.active = :active')
            ->setParameter(':active', true)
            ->andWhere('modeles.username != :username or modeles.username is null')
            ->andWhere('helperusers.idUtilisateur = :usernameid and helperusers.contribution = :contribution')
            ->setParameter(':username', $this->getSessionUser()['username'])
            ->setParameter(':usernameid', $this->getSessionUser()['id'])
            ->setParameter(':contribution', 'photographe')
        ;

        return new JsonResponseFromObject($qb->getQuery()->getResult());
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/v2/model/unassigned/all")
     */
    public function getUnassignedModels(): JsonResponse
    {
        return new JsonResponseFromObject(
            $this->getEm('edgecreator')->getRepository(TranchesEnCoursModeles::class)->findBy([
                'username' => null
            ]
        ));
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/edgecreator/v2/model/{publicationCode}/{issueNumber}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"})
     */
    public function getV2Model(string $publicationCode, string $issueNumber): Response
    {
        [$country, $magazine] = explode('/', $publicationCode);
        $model = $this->getEm('edgecreator')->getRepository(TranchesEnCoursModeles::class)->findOneBy([
            'pays' => $country,
            'magazine' => $magazine,
            'numero' => $issueNumber
        ]);

        if (is_null($model)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        return new JsonResponseFromObject($model);
    }

    /**
     * @Route(
     *     methods={"PUT"},
     *     path="/edgecreator/v2/model/{publicationCode}/{issueNumber}/{isEditor}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"})
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createModel(string $publicationCode, string $issueNumber, string $isEditor): Response
    {
        $ecEm = $this->getEm('edgecreator');
        [$country, $publication] = explode('/', $publicationCode);

        $model = new TranchesEnCoursModeles();
        $model->setPays($country);
        $model->setMagazine($publication);
        $model->setNumero($issueNumber);
        $model->setUsername($isEditor === '1' ? $this->getSessionUser()['username'] : null);
        $model->setActive(true);

        $ecEm->persist($model);
        $ecEm->flush();

        return new JsonResponse(['modelid' => $model->getId()]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     path="/edgecreator/v2/model/clone/to/{publicationCode}/{issueNumber}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"})
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function cloneSteps(Request $request, string $publicationCode, string $issueNumber): Response
    {
        /** @var string[] $steps */
        $steps = $request->request->get('steps');

        $targetModelId = null;
        $deletedSteps = 0;

        /** @var TranchesEnCoursModeles $targetModel */
        $targetModel = $this->getEm('edgecreator')->getRepository(TranchesEnCoursModeles::class)->findOneBy([
            'pays' => explode('/', $publicationCode)[0],
            'magazine' => explode('/', $publicationCode)[1],
            'numero' => $issueNumber
        ]);
        if (is_null($targetModel)) {
            $targetModelId = self::getResponseIdFromServiceResponse(
                $this->createModel($publicationCode, $issueNumber, '1'),
                'modelid'
            );
        }
        else {
            $targetModelId = $targetModel->getId();
            $deletedSteps = $this->deleteSteps($targetModelId);
        }
        $this->assignModel($targetModelId);

        $valueIds = [];
        /** @var array $stepOptions */
        foreach($steps as $stepNumber => $stepOptions) {
            try {
                $this->checkStepOptions($stepOptions['options'], $stepNumber);
                $valueIds[$stepNumber] = $this->createStepV2($targetModelId, $stepNumber, $stepOptions['options'], $stepOptions['stepfunctionname']);
            }
            catch(InvalidArgumentException $e) {}
        }
        return new JsonResponse([
            'modelid' => $targetModelId,
            'valueids' => $valueIds,
            'deletedsteps' => $deletedSteps
        ]);
    }

    /**
     * @Route(methods={"POST"}, path="/edgecreator/v2/step/{modelId}/{stepNumber}")
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createOrUpdateStep(Request $request, int $modelId, int $stepNumber): Response
    {
        $stepFunctionName = $request->request->get('stepfunctionname');
        $optionValues = $request->request->get('options');

        $this->checkStepOptions($optionValues, $stepNumber);
        $valueIds = $this->createStepV2($modelId, $stepNumber, $optionValues, $stepFunctionName);
        return new JsonResponse(['valueids' => $valueIds]);
    }

    /**
     * @Route(methods={"POST"}, path="/edgecreator/v2/step/shift/{modelId}/{stepNumber}/{isIncludingThisStep}")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function shiftStep(int $modelId, int $stepNumber, string $isIncludingThisStep): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);

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
    }

    /**
     * @Route(methods={"POST"}, path="/edgecreator/v2/step/clone/{modelId}/{stepNumber}/to/{newStepNumber}")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function cloneStep(int $modelId, int $stepNumber, int $newStepNumber): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $criteria = [
            'idModele' => $modelId,
            'ordre' => $stepNumber
        ];
        /** @var TranchesEnCoursValeurs[] $values */
        $values = $ecEm->getRepository(TranchesEnCoursValeurs::class)->findBy($criteria);

        if (count($values) === 0) {
            throw new InvalidArgumentException('No values to clone for '.json_encode($criteria, true));
        }

        $functionName = $values[0]->getNomFonction();

        $newStepNumbers = array_map(function(TranchesEnCoursValeurs $value) use ($ecEm, $newStepNumber) {
            $oldStepNumber = $value->getOrdre();
            $newValue = new TranchesEnCoursValeurs();
            $newValue->setIdModele($value->getIdModele());
            $newValue->setNomFonction($value->getNomFonction());
            $newValue->setOptionNom($value->getOptionNom());
            $newValue->setOptionValeur($value->getOptionValeur());
            $newValue->setOrdre($newStepNumber);
            $ecEm->persist($newValue);

            return [['old' => $oldStepNumber, 'new' => $newValue->getOrdre()]];
        }, $values);

        $uniqueStepChanges = array_values(array_unique($newStepNumbers, SORT_REGULAR ));

        $ecEm->flush();

        return new JsonResponse(['newStepNumbers' => array_unique($uniqueStepChanges), 'functionName' => $functionName]);
    }

    /**
     * @Route(methods={"DELETE"}, path="/edgecreator/v2/step/{modelId}/{stepNumber}")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteStep(int $modelId, int $stepNumber): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $qb = $ecEm->createQueryBuilder();

        $qb->delete(TranchesEnCoursValeurs::class, 'values')
            ->andWhere($qb->expr()->eq('values.idModele', ':modelId'))
            ->setParameter(':modelId', $modelId)
            ->andWhere($qb->expr()->eq('values.ordre', ':stepNumber'))
            ->setParameter(':stepNumber', $stepNumber);
        $qb->getQuery()->execute();

        $ecEm->flush();

        return new JsonResponse(['removed' => ['model' => $modelId, 'step' => $stepNumber ]]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/edgecreator/myfontspreview/{foregroundColor}/{backgroundColor}/{width}/font/{fontUrl}/text/{text}",
     *     requirements={"fontUrl"="^[-a-z0-9]+/[-a-z0-9]+(/[-a-z0-9]+)?$"})
     */
    public function getMyFontsPreview(Request $request, string $foregroundColor, string $backgroundColor, string $width, string $text, string $fontUrl): Response
    {
        $text=str_replace("'","\'",preg_replace('#[ ]+\.$#','',$text));

        $ecEm = $this->getEm('edgecreator');
        $criteria = [
            'font' => $fontUrl,
            'color' => $foregroundColor,
            'colorbg' => $backgroundColor,
            'width' => $width,
            'texte' => $text,
        ];
        $textImage = $ecEm->getRepository(ImagesMyfonts::class)->findOneBy($criteria);
        return is_null($textImage)
            ? new JsonResponseFromObject($criteria, 404)
            : new JsonResponseFromObject(array_merge($criteria, ['result' => $textImage]));
    }

    /**
     * @Route(methods={"PUT"}, path="/edgecreator/myfontspreview")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function storeMyFontsPreview(Request $request): Response
    {
        $preview = new ImagesMyfonts();

        $preview->setFont($request->request->get('font'));
        $preview->setColor($request->request->get('fgColor'));
        $preview->setColorbg($request->request->get('bgColor'));
        $preview->setWidth($request->request->get('width'));
        $preview->setTexte($request->request->get('text'));
        $preview->setPrecision($request->request->get('precision'));

        $ecEm = $this->getEm('edgecreator');
        $ecEm->persist($preview);
        $ecEm->flush();

        return new JsonResponse(['previewid' => $preview->getId()]);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgecreator/v2/myfontspreview")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function storeMyFontsPreviewV2(Request $request): Response
    {
        $preview = new ImagesMyfonts();

        $preview->setFont($request->request->get('font'));
        $preview->setColor($request->request->get('color'));
        $preview->setColorbg($request->request->get('colorbg'));
        $preview->setWidth($request->request->get('width'));
        $preview->setTexte($request->request->get('texte'));
        $preview->setPrecision($request->request->get('precision'));

        $ecEm = $this->getEm('edgecreator');
        $ecEm->persist($preview);
        $ecEm->flush();

        return new JsonResponse(['previewid' => $preview->getId()]);
    }

    /**
     * @Route(methods={"DELETE"}, path="/edgecreator/myfontspreview/{previewId}")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteMyFontsPreview(int $previewId): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $preview = $ecEm->getRepository(ImagesMyfonts::class)->find($previewId);
        $ecEm->remove($preview);
        $ecEm->flush();

        return new JsonResponse(['removed' => [$preview->getId()]]);
    }

    /**
     * @Route(methods={"POST"}, path="/edgecreator/model/v2/{modelId}/deactivate")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deactivateModel(int $modelId): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
        $model->setActive(false);
        $ecEm->persist($model);
        $ecEm->flush();

        return new JsonResponse(['deactivated' => $model->getId()]);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgecreator/model/v2/{modelId}/photo/main")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setModelMainPhoto(Request $request, int $modelId): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $photoName = $request->request->get('photoname');

        /** @var TranchesEnCoursModeles $model */
        $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);

        /** @var Collection|TranchesEnCoursContributeurs[] $helperUsers */
        $helperUsers = $ecEm->getRepository(TranchesEnCoursContributeurs::class)->findBy([
            'idModele' => $modelId
        ]);

        $currentUserId = $this->getSessionUser()['id'];
        if (count(array_filter($helperUsers, function(TranchesEnCoursContributeurs $helperUser) use ($currentUserId) {
                return $helperUser->getIdUtilisateur() === $currentUserId;
            })) === 0) {
            $photographer = new TranchesEnCoursContributeurs();
            $photographer->setIdModele($model);
            $photographer->setContribution('photographe');
            $photographer->setIdUtilisateur($currentUserId);

            $model->setContributeurs(array_merge($helperUsers, [$photographer]));
            $ecEm->persist($model);
            $ecEm->flush();
        }

        $mainPhoto = new ImagesTranches();
        $mainPhoto
            ->setIdUtilisateur($this->getSessionUser()['id'])
            ->setDateheure((new DateTime())->setTime(0,0))
            ->setHash(null) // TODO
            ->setNomfichier($photoName);
        $ecEm->persist($mainPhoto);


        $qbDeletePreviousPhoto = $ecEm->createQueryBuilder();
        $qbDeletePreviousPhoto
            ->delete(TranchesEnCoursModelesImages::class, 'models_photos')
            ->where('models_photos.idModele = :modelid')
            ->setParameter('modelid', $modelId);
        $qbDeletePreviousPhoto->getQuery()->execute();

        $photoAndEdge = new TranchesEnCoursModelesImages();
        $photoAndEdge
            ->setIdImage($mainPhoto)
            ->setIdModele($model)
            ->setEstphotoprincipale(true);
        $ecEm->persist($photoAndEdge);

        $ecEm->flush();

        return new JsonResponse(['mainphoto' => ['modelid' => $model->getId(), 'photoname' => $mainPhoto->getNomfichier()]]);
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/model/v2/{modelId}/photo/main")
     * @throws NonUniqueResultException
     */
    public function getModelMainPhoto(int $modelId): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $qb = $ecEm->createQueryBuilder();

        $qb->select('photo.id, photo.nomfichier')
            ->from(TranchesEnCoursModelesImages::class, 'modelsPhotos')
            ->innerJoin('modelsPhotos.idModele', 'model')
            ->innerJoin('modelsPhotos.idImage', 'photo')
            ->andWhere('model.id = :modelId')
            ->setParameter(':modelId', $modelId)
            ->andWhere('modelsPhotos.estphotoprincipale = 1');

        try {
            $mainPhoto = $qb->getQuery()->getSingleResult();
        }
        catch (NoResultException $e) {
            return new Response("No photo found for model $modelId", Response::HTTP_NO_CONTENT);
        }

        return new JsonResponseFromObject($mainPhoto);
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/multiple_edge_photo/today")
     * @throws Exception
     */
    public function getMultipleEdgePhotosFromToday(): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $qb = $ecEm->createQueryBuilder();

        $qb->select('photo.id, photo.hash, photo.dateheure, photo.idUtilisateur')
            ->from(ImagesTranches::class, 'photo')
            ->andWhere('photo.idUtilisateur = :idUtilisateur')
            ->setParameter(':idUtilisateur', $this->getSessionUser()['id'])
            ->andWhere('photo.dateheure = :today')
            ->setParameter(':today', new DateTime('today'));

        $uploadedFiles = $qb->getQuery()->getResult();

        return new JsonResponseFromObject($uploadedFiles);
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/multiple_edge_photo/hash/{hash}")
     */
    public function getMultipleEdgePhotoFromHash(string $hash): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $uploadedFile = $ecEm->getRepository(ImagesTranches::class)->findOneBy([
            'idUtilisateur' => $this->getSessionUser()['id'],
            'hash' => $hash
        ]);
        return new JsonResponseFromObject($uploadedFile);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgecreator/multiple_edge_photo")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createMultipleEdgePhoto(Request $request, Swift_Mailer $mailer): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $hash = $request->request->get('hash');
        $fileName = $request->request->get('filename');
        $user = $this->getSessionUser();

        $photo = new ImagesTranches();
        $photo->setHash($hash);
        $photo->setDateheure(new DateTime('today'));
        $photo->setNomfichier($fileName);
        $photo->setIdUtilisateur($user['id']);
        $ecEm->persist($photo);
        $ecEm->flush();

        $message = new Swift_Message();
        $message
            ->setSubject('Nouvelle photo de tranche')
            ->setFrom([$user['username']. '@' .$_ENV['SMTP_ORIGIN_EMAIL_DOMAIN_EDGECREATOR']])
            ->setTo([$_ENV['SMTP_USERNAME']])
            ->setBody($_ENV['IMAGE_UPLOAD_ROOT'].$fileName);

        $failures = [];
        if (!$mailer->send($message, $failures)) {
            throw new RuntimeException("Can't send e-mail '$message': failed with ".print_r($failures, true));
        }

        return new JsonResponse(['photo' => ['id' => $photo->getId()]]);
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/elements/images/{nameSubString}")
     * @throws DBALException
     */
    public function getElementImagesByNameSubstring(string $nameSubString): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $templatedValues = $ecEm->getConnection()->executeQuery('
            SELECT Pays, Magazine, Option_valeur, Numero_debut, Numero_fin
            FROM edgecreator_valeurs valeurs
              INNER JOIN edgecreator_modeles2 modeles ON valeurs.ID_Option = modeles.ID
              INNER JOIN edgecreator_intervalles intervalles ON valeurs.ID = intervalles.ID_Valeur
            WHERE Nom_fonction = :functionName AND Option_nom = :optionName AND (Option_valeur = :optionValue OR (Option_valeur LIKE :optionValueTemplate AND Option_valeur LIKE :optionValueExtension))
            GROUP BY Pays, Magazine, Ordre, Option_nom, Numero_debut, Numero_fin
            UNION
            SELECT Pays, Magazine, Option_valeur, Numero AS Numero_debut, Numero AS Numero_fin
            FROM tranches_en_cours_modeles modeles
              INNER JOIN tranches_en_cours_valeurs valeurs ON modeles.ID = valeurs.ID_Modele
            WHERE Nom_fonction = :functionName AND Option_nom = :optionName AND (Option_valeur = :optionValue OR (Option_valeur LIKE :optionValueTemplate AND Option_valeur LIKE :optionValueExtension))',
            [
                'functionName' => 'Image',
                'optionName' => 'Source',
                'optionValue' => $nameSubString,
                'optionValueTemplate' => '%[Numero]%',
                'optionValueExtension' => '%.png',
            ], [
            Types::STRING,
            Types::STRING,
            Types::STRING,
            Types::STRING,
            Types::STRING,
        ])->fetchAll();

        $matches = array_filter($templatedValues, function($match) use ($nameSubString) {
            $string_chunks = preg_split('/\[[^]]+]/', $match['Option_valeur']);
            foreach($string_chunks as $string_chunk) {
                if (strpos($nameSubString, $string_chunk) === false) {
                    return false;
                }
            }
            return true;
        });
        return new JsonResponseFromObject(array_values($matches));
    }

    /**
     * @Route(methods={"GET"}, path="/edgecreator/contributors/{modelId}")
     */
    public function getModelContributors(string $modelId) : Response {
        $ecEm = $this->getEm('edgecreator');

        return new JsonResponseFromObject(
            $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId)->getContributeurs()->toArray()
        );
    }

    /**
     * @Route(
     *     methods={"PUT"},
     *     path="/edgecreator/publish/{publicationCode}/{issueNumber}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"}
     * )
     * @throws ORMException
     * @throws Exception
     */
    public function publishEdgeFromIssuenumber(Request $request, ContributionService $contributionService, string $publicationCode, string $issueNumber) {
        $designers = $request->request->get('designers');
        $photographers = $request->request->get('photographers');

        $modelContributors = array_merge(
            array_map(function($userId) {
                return ['userId' => $userId, 'contribution' => 'createur'];
            }, $this->getUserIdsByUsername($designers)),
            array_map(function($userId) {
                return ['userId' => $userId, 'contribution' => 'photographe'];
            }, $this->getUserIdsByUsername($photographers))
        );

        ['edgeId' => $edgeId, 'contributors' => $contributors] =
            $this->publishEdgeOnDm($contributionService, $modelContributors, $publicationCode, $issueNumber);

        [$countryCode, $shortPublicationCode] = explode('/', $publicationCode);
        return new JsonResponse([
            'publicationCode' => $publicationCode,
            'issueNumber' => $issueNumber,
            'edgeId' => $edgeId,
            'url' => "{$_ENV['EDGES_ROOT']}/$countryCode/gen/$shortPublicationCode.$issueNumber.png",
            'contributors' => $contributors
        ]);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgecreator/publish/{modelId}")
     * @throws ORMException
     * @throws Exception
     */
    public function publishEdgeFromModel(Request $request, ContributionService $contributionService, string $modelId) : Response {
        $ecEm = $this->getEm('edgecreator');

        /** @var TranchesEnCoursModeles $edgeModelToPublish */
        $edgeModelToPublish = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);

        if (!is_null($edgeModelToPublish)) {
            $designers = $request->request->get('designers');
            $photographers = $request->request->get('photographers');
            $this->updateModelContributors($edgeModelToPublish, $designers, $photographers);

            $publicationCode = implode('/', [$edgeModelToPublish->getPays(), $edgeModelToPublish->getMagazine()]);

            $modelContributors = array_map(function(TranchesEnCoursContributeurs $contributor) {
                return [
                    'userId' => $contributor->getIdUtilisateur(),
                    'contribution' => $contributor->getContribution(),
                ];
            }, $edgeModelToPublish->getContributeurs());

            ['edgeId' => $edgeId, 'contributors' => $contributors] =
                $this->publishEdgeOnDm($contributionService, $modelContributors, $publicationCode, $edgeModelToPublish->getNumero());

            $edgeModelToPublish->setActive(false);
            $ecEm->persist($edgeModelToPublish);
            $ecEm->flush();

            return new JsonResponse([
                'publicationCode' => $publicationCode,
                'issueNumber' => $edgeModelToPublish->getNumero(),
                'edgeId' => $edgeId,
                'url' => "{$_ENV['EDGES_ROOT']}/{$edgeModelToPublish->getPays()}/gen/{$edgeModelToPublish->getMagazine()}.{$edgeModelToPublish->getNumero()}.png",
                'contributors' => $contributors
            ]);
        }
        return new Response("$modelId is not a non-published model", Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function publishEdgeOnDm(ContributionService $contributionService, array $contributors, string $publicationCode, string $issueNumber) : array {
        $dmEm = $this->getEm('dm');

        if (!is_null($existingEdge = $dmEm->getRepository(TranchesPretes::class)->findOneBy([
            'publicationcode' => $publicationCode,
            'issuenumber' => $issueNumber
        ]))) {
            $edgeToPublish = $existingEdge;
        }
        else {
            $edgeToPublish = new TranchesPretes();
        }
        $dmEm->persist($edgeToPublish
            ->setPublicationcode($publicationCode)
            ->setIssuenumber($issueNumber)
            ->setDateajout(new DateTime())
        );

        [$countryCode, $shortPublicationCode] = explode('/', $edgeToPublish->getPublicationcode());
        /** @var NumerosPopularite $popularity */
        $issuePopularity = $dmEm->getRepository(NumerosPopularite::class)->findOneBy([
            'pays' => $countryCode,
            'magazine' => $shortPublicationCode,
            'numero' => $edgeToPublish->getIssuenumber()
        ]);
        $popularity = is_null($issuePopularity) ? 0 : $issuePopularity->getPopularite();

        $contributions = [];
        foreach($contributors as $contributor) {
            $userId = $contributor['userId'];
            $contribution = $contributor['contribution'];
            $contributions[]=$contributionService->persistContribution(
                $dmEm->getRepository(Users::class)->find($userId),
                $contribution,
                $popularity,
                $edgeToPublish
            );
        }

        $dmEm->persist($edgeToPublish);
        $dmEm->flush();

        return [
            'edgeId' => $edgeToPublish->getId(),
            'contributors' => array_map(function(UsersContributions $contribution) {
                return $contribution->getUser()->getId();
            }, $contributions)
        ];
    }

    private function getUserIdsByUsername(array $usernames) : array {
        $dmEm = $this->getEm('dm');

        $qb = $dmEm->createQueryBuilder();
        $qb->select('users.id, users.username')
            ->from(Users::class, 'users')
            ->andWhere('users.username in (:usernames)')
            ->setParameter(':usernames', $usernames);

        $contributorsIdsResults = $qb->getQuery()->getResult();

        $contributorsIds = [];
        array_walk($contributorsIdsResults, function($value) use (&$contributorsIds) {
            $contributorsIds[$value['username']] = $value['id'];
        });
        return $contributorsIds;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function updateModelContributors(TranchesEnCoursModeles $modelId, array $designers, array $photographers) : void {
        $ecEm = $this->getEm('edgecreator');

        $contributorsIds = $this->getUserIdsByUsername(
            array_merge($designers ?? [], $photographers ?? [])
        );

        function addNewContributors($modelId, &$contributors, $newContributors, $contributorsIds, $contributionType) {
            if (!is_null($newContributors)) {
                foreach ($newContributors as $newContributorUsername) {
                    $contributorId = $contributorsIds[$newContributorUsername];
                    $contributorExists = count(array_filter($contributors, function(TranchesEnCoursContributeurs $existingContributor) use ($contributionType, $contributorId) {
                        return $existingContributor->getIdUtilisateur() === $contributorId
                            && $existingContributor->getContribution() === $contributionType;
                    })) > 0;
                    if (!$contributorExists) {
                        $newContributor = new TranchesEnCoursContributeurs();
                        $contributors[] = $newContributor
                            ->setContribution($contributionType)
                            ->setIdUtilisateur($contributorId)
                            ->setIdModele($modelId);
                    }
                }
            }
        }

        $qbDeleteExistingContributors = $this->getEm('edgecreator')->createQueryBuilder();
        $qbDeleteExistingContributors
            ->delete(TranchesEnCoursContributeurs::class, 'contributors')
            ->where('contributors.idModele = :modelid')
            ->setParameter('modelid', $modelId);
        $qbDeleteExistingContributors->getQuery()->execute();

        $contributors = [];

        addNewContributors($modelId, $contributors, $photographers, $contributorsIds, 'photographe');
        addNewContributors($modelId, $contributors, $designers, $contributorsIds, 'createur');

        $modelId->setContributeurs($contributors);

        $ecEm->persist($modelId);
        $ecEm->flush();
    }

    private function createStepV1(string $publicationCode, int $stepNumber, string $functionName, string $optionName): int
    {
        $ecEm = $this->getEm('edgecreator');

        [$country, $publication] = explode('/', $publicationCode);

        $model = new EdgecreatorModeles2();
        $model->setPays($country);
        $model->setMagazine($publication);
        $model->setOrdre($stepNumber);
        $model->setNomFonction($functionName);
        $model->setOptionNom($optionName);

        $ecEm->persist($model);
        $ecEm->flush();

        return $model->getId();
    }

    private function createValueV1(string $optionId, string $optionValue) : int {
        $ecEm = $this->getEm('edgecreator');

        $value = new EdgecreatorValeurs();
        $value->setIdOption($optionId);
        $value->setOptionValeur($optionValue);

        $ecEm->persist($value);
        $ecEm->flush();

        return $value->getId();
    }

    private function createIntervalV1(int $valueId, string $firstIssueNumber, string $lastIssueNumber) : int {
        $ecEm = $this->getEm('edgecreator');
        $interval = new EdgecreatorIntervalles();

        $interval->setIdValeur($valueId);
        $interval->setNumeroDebut($firstIssueNumber);
        $interval->setNumeroFin($lastIssueNumber);
        $interval->setUsername($this->getSessionUser()['username']);

        $ecEm->persist($interval);
        $ecEm->flush();

        return $interval->getId();
    }

    private function deleteSteps(int $modelId) : int {
        $qbDeleteSteps = $this->getEm('edgecreator')->createQueryBuilder();
        $qbDeleteSteps
            ->delete(TranchesEnCoursValeurs::class, 'values')
            ->where('values.idModele = :modelid')
            ->setParameter('modelid', $modelId);

        return $qbDeleteSteps->getQuery()->execute();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function assignModel(int $modelId): Response
    {
        $ecEm = $this->getEm('edgecreator');
        $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);
        $model->setUsername($this->getSessionUser()['username']);

        $ecEm->persist($model);
        $ecEm->flush();

        return new Response();
    }

    /**
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createStepV2(int $modelId, int $stepNumber, $options, ?string $newFunctionName): array
    {
        $ecEm = $this->getEm('edgecreator');
        $qb = $ecEm->createQueryBuilder();

        /** @var TranchesEnCoursModeles $model */
        $model = $ecEm->getRepository(TranchesEnCoursModeles::class)->find($modelId);

        if (is_null($newFunctionName)) {
            /** @var ?TranchesEnCoursValeurs $existingValue */
            $existingValue = $ecEm->getRepository(TranchesEnCoursValeurs::class)->findOneBy([
                'idModele' => $modelId,
                'ordre' => $stepNumber
            ]);

            if (is_null($existingValue)) {
                throw new InvalidArgumentException('No option exists for this step and no function name was provided');
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
            $optionToCreate->setOrdre($stepNumber);
            $optionToCreate->setNomFonction($newFunctionName);
            $optionToCreate->setOptionNom($optionName);
            $optionToCreate->setOptionValeur($optionValue);

            $ecEm->persist($optionToCreate);
            $createdOptions[] = ['name' => $optionName, 'value' => $optionValue];
        });

        $ecEm->flush();
        $ecEm->clear();

        return $createdOptions;
    }

    private function checkStepOptions($options, int $stepNumber) : void {
        if (is_null($options)) {
            throw new InvalidArgumentException('No options provided, ignoring step '.$stepNumber);
        }
        if (!is_array($options)) {
            throw new InvalidArgumentException('Invalid options input : '.print_r($options, true));
        }
    }
}
