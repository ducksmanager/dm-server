<?php

namespace DmServer\Controllers\Collection;

use Dm\Contracts\Results\UpdateCollectionResult;
use Dm\Models\Achats;
use Dm\Models\Numeros;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM, $function);
    }
    
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/collection/issues',
            function (Request $request, Application $app) {
                return self::wrapInternalService($app, function(EntityManager $dmEm) use ($app) {
                    /** @var Numeros[] $issues */
                    $issues = $dmEm->getRepository(Numeros::class)->findBy(
                        ['idUtilisateur' => self::getSessionUser($app)['id']],
                        ['pays' => 'asc', 'magazine' => 'asc', 'numero' => 'asc']
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issues));
                });
            }
        );

        $routing->delete(
            '/internal/collection/issues',
            function (Request $request, Application $app) {
                return self::wrapInternalService($app, function(EntityManager $dmEm) use ($app, $request) {
                    $country = $request->request->get('country');
                    $publication = $request->request->get('publication');
                    $issuenumbers = $request->request->get('issuenumbers');

                    $qb = $dmEm->createQueryBuilder();
                    $qb
                        ->delete(Numeros::class, 'issues')

                        ->andWhere($qb->expr()->eq('issues.pays', ':country'))
                        ->setParameter(':country', $country)

                        ->andWhere($qb->expr()->eq('issues.magazine', ':publication'))
                        ->setParameter(':publication', $publication)

                        ->andWhere($qb->expr()->in('issues.numero', ':issuenumbers'))
                        ->setParameter(':issuenumbers', $issuenumbers)

                        ->andWhere($qb->expr()->in('issues.idUtilisateur', ':userId'))
                        ->setParameter(':userId', self::getSessionUser($app)['id']);

                    $nbRemoved = $qb->getQuery()->getResult();

                    $deletionResult = new UpdateCollectionResult('DELETE', $nbRemoved);

                    return new JsonResponse(ModelHelper::getSimpleArray([$deletionResult]));
                });
            }
        );

        $routing->post(
            '/internal/collection/purchases/{purchaseId}',
            function (Request $request, Application $app, $purchaseId) {
                return self::wrapInternalService($app, function (EntityManager $dmEm) use ($app, $request, $purchaseId) {

                    $purchaseDate = $request->request->get('date');
                    $purchaseDescription = $request->request->get('description');
                    $idUser = self::getSessionUser($app)['id'];

                    if (!is_null($purchaseId)) {
                        $purchase = $dmEm->getRepository(Achats::class)->findOneBy(['idAcquisition' => $purchaseId, 'idUser' => $idUser]);
                        if (is_null($purchase)) {
                            return new Response('You don\'t have the rights to update this purchase', Response::HTTP_UNAUTHORIZED);
                        }
                    }
                    else {
                        $purchase = new Achats();
                    }

                    $purchase->setIdUser($idUser);
                    $purchase->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $purchaseDate.' 00:00:00'));
                    $purchase->setDescription($purchaseDescription);

                    $dmEm->persist($purchase);
                    $dmEm->flush();

                    return new Response();
                });
            }
        )->value('purchaseId', null);

        $routing->post(
            '/internal/collection/issues',
            function (Request $request, Application $app) {
                return self::wrapInternalService($app, function(EntityManager $dmEm) use ($app, $request) {
                    $country = $request->request->get('country');
                    $publication = $request->request->get('publication');
                    $issuenumbers = $request->request->get('issuenumbers');

                    $condition = $request->request->get('condition');
                    $conditionNewIssues = is_null($condition) ? 'possede' : $condition;

                    $istosell = $request->request->get('istosell');
                    $istosellNewIssues = is_null($istosell) ? false : $istosell;

                    $purchaseid = $request->request->get('purchaseid');
                    $purchaseidNewIssues = is_null($purchaseid) ? -2 : $purchaseid; // TODO allow NULL

                    $qb = $dmEm->createQueryBuilder();
                    $qb
                        ->select('issues')
                        ->from(Numeros::class, 'issues')

                        ->andWhere($qb->expr()->eq('issues.pays', ':country'))
                        ->setParameter(':country', $country)

                        ->andWhere($qb->expr()->eq('issues.magazine', ':publication'))
                        ->setParameter(':publication', $publication)

                        ->andWhere($qb->expr()->in('issues.numero', ':issuenumbers'))
                        ->setParameter(':issuenumbers', $issuenumbers)

                        ->andWhere($qb->expr()->eq('issues.idUtilisateur', ':userId'))
                        ->setParameter(':userId', self::getSessionUser($app)['id'])

                        ->indexBy('issues', 'issues.numero');

                    /** @var Numeros[] $existingIssues */
                    $existingIssues = $qb->getQuery()->getResult();

                    foreach($existingIssues as $existingIssue) {
                        if (!is_null($condition)) {
                            $existingIssue->setEtat($condition);
                        }
                        if (!is_null($istosell)) {
                            $existingIssue->setAv($istosell);
                        }
                        if (!is_null($purchaseid)) {
                            $existingIssue->setIdAcquisition($purchaseid);
                        }
                        $dmEm->persist($existingIssue);
                    }

                    $issueNumbersToCreate = array_diff($issuenumbers, array_keys($existingIssues));
                    foreach($issueNumbersToCreate as $issueNumberToCreate) {
                        $newIssue = new Numeros();
                        $newIssue->setPays($country);
                        $newIssue->setMagazine($publication);
                        $newIssue->setNumero($issueNumberToCreate);
                        $newIssue->setEtat($conditionNewIssues);
                        $newIssue->setAv($istosellNewIssues);
                        $newIssue->setIdAcquisition($purchaseidNewIssues);
                        $newIssue->setIdUtilisateur(self::getSessionUser($app)['id']);

                        $dmEm->persist($newIssue);
                    }

                    $dmEm->flush();
                    $dmEm->clear();

                    $updateResult = new UpdateCollectionResult('UPDATE', count($existingIssues));
                    $creationResult = new UpdateCollectionResult('CREATE', count($issueNumbersToCreate));

                    return new JsonResponse(ModelHelper::getSimpleArray([$updateResult, $creationResult]));
                });
            }
        );
    }
}
