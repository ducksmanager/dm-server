<?php

namespace DmServer\Controllers\Collection;

use Dm\Contracts\Results\UpdateCollectionResult;
use Dm\Models\Numeros;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
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
        $routing->get(
            '/internal/collection/issues',
            function (Request $request, Application $app) {
                return AbstractController::return500ErrorOnException($app, function() use ($app) {
                    /** @var Numeros[] $issues */
                    $issues = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Numeros::class)->findBy(
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
                return AbstractController::return500ErrorOnException($app, function() use ($app, $request) {
                    $country = $request->request->get('country');
                    $publication = $request->request->get('publication');
                    $issuenumbers = $request->request->get('issuenumbers');

                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->createQueryBuilder();
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
            '/internal/collection/issues',
            function (Request $request, Application $app) {
                return AbstractController::return500ErrorOnException($app, function() use ($app, $request) {

                    $dmEm = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM);

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
