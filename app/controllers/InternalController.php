<?php

namespace DmServer;

use Coa\Models\InducksCountryname;
use Coa\Models\InducksIssue;
use Coa\Models\InducksPublication;
use CoverId\Models\Covers;
use Coa\Contracts\Results\SimpleIssueWithUrl;
use Dm\Contracts\Results\UpdateCollectionResult;
use Dm\Models\Numeros;
use Dm\Models\Users;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalController extends AppController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/user/exists/{username}',
            function (Request $request, Application $app, $username) {
                return AppController::return500ErrorOnException($app, function() use ($username) {
                    $existingUser = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(array(
                        'username' => $username
                    ));
                    if (count($existingUser) > 0) {
                        return new Response('', Response::HTTP_CONFLICT);
                    }
                    return new Response('', Response::HTTP_OK);
                });
            }
        );
        $routing->get(
            '/internal/user/new/check/{username}/{password}/{password2}',
            function (Request $request, Application $app, $username, $password, $password2) {

                $error = null;

                if (isset($username)) {
                    if (strlen($username) <3) {
                        $error='UTILISATEUR_3_CHAR_ERREUR';
                    }
                    if (strlen($password) <6) {
                        $error='MOT_DE_PASSE_6_CHAR_ERREUR';
                    }
                    elseif ($password !== $password2) {
                        $error='MOTS_DE_PASSE_DIFFERENTS';
                    }
                    else {
                        if (!(self::callInternal($app, '/user/exists', 'GET', [$username])
                            ->isSuccessful())) {
                            return new Response(self::$translator->trans('UTILISATEUR_EXISTANT'), Response::HTTP_CONFLICT);
                        }
                    }
                }
                if (is_null($error)) {
                    return new Response('OK', Response::HTTP_OK);
                }

                return new Response(self::$translator->trans($error), Response::HTTP_PRECONDITION_FAILED);
            }
        );

        $routing->get(
            '/internal/user/check/{username}/{password}',
            function (Request $request, Application $app, $username, $password) {
                return AppController::return500ErrorOnException($app, function() use ($username, $password) {
                    $existingUser = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(array(
                        'username' => $username,
                        'password' => sha1($password)
                    ));
                    if (count($existingUser) > 0) {
                        return new Response($existingUser[0]->getId(), Response::HTTP_OK);
                    } else {
                        return new Response('', Response::HTTP_UNAUTHORIZED);
                    }
                });
            }
        );

        $routing->put('/internal/user/new', function (Request $request, Application $app) {
            return AppController::return500ErrorOnException($app, function() use ($request) {
                $user = new Users();
                $user->setUsername($request->request->get('username'));
                $user->setPassword(sha1($request->request->get('password')));
                $user->setEmail($request->request->get('email'));
                $user->setDateinscription(new \DateTime());

                DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->persist($user);
                DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->flush();

                return new Response('OK', Response::HTTP_CREATED);
            });
        });

        $routing->put('/internal/collection/add', function (Request $request, Application $app) {
            return AppController::return500ErrorOnException($app, function() use ($request, $app) {
                $issue = new Numeros();
                $issue->setPays($request->request->get('country'));
                $issue->setMagazine($request->request->get('publication'));
                $issue->setNumero($request->request->get('issuenumber'));
                $issue->setEtat($request->request->get('condition'));
                $issue->setIdUtilisateur(self::getSessionUser($app)['id']);

                DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->persist($issue);
                DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->flush();

                return new Response('OK', Response::HTTP_CREATED);
            });
        });

        $routing->get(
            '/internal/collection/issues',
            function (Request $request, Application $app) {
                return AppController::return500ErrorOnException($app, function() use ($app) {
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
                return AppController::return500ErrorOnException($app, function() use ($app, $request) {
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
                return AppController::return500ErrorOnException($app, function() use ($app, $request) {

                    $country = $request->request->get('country');
                    $publication = $request->request->get('publication');
                    $issuenumbers = $request->request->get('issuenumbers');

                    $condition = $request->request->get('condition');
                    $conditionNewIssues = is_null($condition) ? 'possede' : $condition;

                    $istosell = $request->request->get('istosell');
                    $istosellNewIssues = is_null($istosell) ? false : $istosell;

                    $purchaseid = $request->request->get('purchaseid');
                    $purchaseidNewIssues = is_null($purchaseid) ? -2 : $purchaseid; // TODO allow NULL

                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->createQueryBuilder();
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
                        DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->persist($existingIssue);
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

                        DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->persist($newIssue);
                    }

                    DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->flush();

                    $updateResult = new UpdateCollectionResult('UPDATE', count($existingIssues));
                    $creationResult = new UpdateCollectionResult('CREATE', count($issueNumbersToCreate));

                    return new JsonResponse(ModelHelper::getSimpleArray([$updateResult, $creationResult]));
                });
            }
        );

        $routing->get(
            '/internal/coa/countrynames/{countryCodes}',
            function (Request $request, Application $app, $countryCodes) {
                return AppController::return500ErrorOnException($app, function() use ($countryCodes) {
                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qb
                        ->select('inducks_countryname.countrycode, inducks_countryname.countryname')
                        ->from(InducksCountryname::class, 'inducks_countryname');

                    if ($countryCodes !== null) {
                        $qb->where($qb->expr()->in('inducks_countryname.countrycode', explode(',', $countryCodes)));
                    }

                    $results = $qb->getQuery()->getResult();
                    $countryNames = array();
                    array_walk(
                        $results,
                        function($result) use (&$countryNames) {
                            $countryNames[$result['countrycode']] = $result['countryname'];
                        }
                    );
                    return new JsonResponse(ModelHelper::getSerializedArray($countryNames));
                });
            }
        )->value('countryCodes', null);

        $routing->get(
            '/internal/coa/publicationtitles/{publicationCodes}',
            function (Request $request, Application $app, $publicationCodes) {
                return AppController::return500ErrorOnException($app, function() use ($publicationCodes) {
                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qb
                        ->select('inducks_publication.publicationcode, inducks_publication.title')
                        ->from(InducksPublication::class, 'inducks_publication');

                    if (preg_match('#^[a-z]+/%$#', $publicationCodes)) {
                        $qb->where($qb->expr()->like('inducks_publication.publicationcode', "'".$publicationCodes."'"));
                    }
                    else {
                        $qb->where($qb->expr()->in('inducks_publication.publicationcode', explode(',', $publicationCodes)));
                    }

                    $results = $qb->getQuery()->getResult();
                    $publicationTitles = array();
                    array_walk(
                        $results,
                        function($result) use (&$publicationTitles) {
                            $publicationTitles[$result['publicationcode']] = $result['title'];
                        }
                    );
                    return new JsonResponse(ModelHelper::getSerializedArray($publicationTitles));
                });
            }
        )->assert('publicationCodes', '.+');

        $routing->get(
            '/internal/coa/issues/{publicationCode}',
            function (Request $request, Application $app, $publicationCode) {
                return AppController::return500ErrorOnException($app, function() use ($publicationCode) {
                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qb
                        ->select('inducks_issue.issuenumber')
                        ->from(InducksIssue::class, 'inducks_issue');

                    $qb->where($qb->expr()->eq('inducks_issue.publicationcode', "'".$publicationCode."'"));

                    $results = $qb->getQuery()->getResult();
                    $issueNumbers = array_map(
                        function($issue) {
                            return $issue['issuenumber'];
                        },
                        $results
                    );
                    return new JsonResponse(ModelHelper::getSerializedArray($issueNumbers));
                });
            }
        )->assert('publicationCode', '.+');

        $routing->get(
            '/internal/cover-id/issuecodes/{coverids}',
            function (Request $request, Application $app, $coverids) {
                return AppController::return500ErrorOnException($app, function() use ($coverids) {
                    $coveridsList = explode(',', $coverids);

                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COVER_ID)->createQueryBuilder();
                    $qb
                        ->select('covers.issuecode')
                        ->from(Covers::class, 'covers');

                    $qb->where($qb->expr()->in('covers.id', $coveridsList));

                    $results = $qb->getQuery()->getResult();

                    array_walk(
                        $results,
                        function($issue, $i) use ($coveridsList, &$issueCodes) {
                            $issueCodes[$coveridsList[$i]] = $issue['issuecode'];
                        }
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issueCodes));
                });
            }
        )->assert('coverids', '^([0-9]+,){0,4}[0-9]+$');

        $routing->get(
            '/internal/coa/issuesbycodes/{issuecodes}',
            function (Request $request, Application $app, $issuecodes) {
                return AppController::return500ErrorOnException($app, function() use ($issuecodes) {
                    $issuecodesList = explode(',', $issuecodes);

                    $qbIssueInfo = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qbIssueInfo
                        ->select('inducks_publication.countrycode, inducks_publication.title, inducks_issue.issuenumber, inducks_issue.issuecode')
                        ->from(InducksIssue::class, 'inducks_issue')
                        ->join(InducksPublication::class, 'inducks_publication', Join::WITH, 'inducks_issue.publicationcode = inducks_publication.publicationcode');

                    $qbIssueInfo->where($qbIssueInfo->expr()->in('inducks_issue.issuecode', $issuecodesList));

                    $resultsIssueInfo = $qbIssueInfo->getQuery()->getResult();

                    array_walk(
                        $resultsIssueInfo,
                        function($issue) use (&$issues) {
                            $issues[$issue['issuecode']] = SimpleIssueWithUrl::buildWithoutUrl($issue['countrycode'], $issue['title'], $issue['issuenumber']);
                        }
                    );

                    $qbCoverInfo = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COVER_ID)->createQueryBuilder();
                    $qbCoverInfo
                        ->select('covers.id, covers.url, covers.issuecode')
                        ->from(Covers::class, 'covers');

                    $qbCoverInfo->where($qbCoverInfo->expr()->in('covers.issuecode', $issuecodesList));

                    $resultsCoverInfo = $qbCoverInfo->getQuery()->getResult();

                    array_walk(
                        $resultsCoverInfo,
                        function($issue) use (&$issues) {

                            if (empty($issues[$issue['issuecode']])) {
                                throw new Exception('No COA data exists for this issue : ' . $issue['issuecode']);
                            }
                            /** @var SimpleIssueWithUrl $issueObject */
                            $issueObject = $issues[$issue['issuecode']];
                            $url = $issue['url'];
                            if (strpos($url, 'webusers') === 0) {
                                $url = 'webusers/'.$url;
                            }
                            $issueObject->setFullurl($url);
                        }
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issues));
                });
            }
        )->assert('issuecodes', '^([a-z]+/[- A-Z0-9]+,){0,4}[a-z]+/[- A-Z0-9]+$');


        $routing->get(
            '/internal/cover-id/download/{coverUrl}',
            function (Request $request, Application $app, $coverUrl) {
                return AppController::return500ErrorOnException($app, function() use ($coverUrl) {
                    $localFilePath = DmServer::$settings['image_local_root'] . basename($coverUrl);

                    @mkdir(DmServer::$settings['image_local_root'].dirname($coverUrl), 0777, true);
                    file_put_contents(
                        $localFilePath,
                        file_get_contents(DmServer::$settings['image_remote_root'] . $coverUrl)
                    );

                    return new BinaryFileResponse($localFilePath);
                });
            }
        )->assert('coverUrl', '.+');

        $routing->post(
            '/internal/rawsql',
            function (Request $request, Application $app) {
                return AppController::return500ErrorOnException($app, function() use ($request, $app) {
                    $query = $request->request->get('query');
                    $db = $request->request->get('db');

                    $em = DmServer::getEntityManager($db);
                    if (is_null($em)) {
                        return new Response('Invalid parameter : db='.$db, Response::HTTP_BAD_REQUEST);
                    }

                    if (isset($app['monolog'])) {
                        $app['monolog']->addInfo('Raw sql sent : '.$query);
                    }
                    $results = $em->getConnection()->fetchAll($query);
                    return new JsonResponse($results);
                });
            }
        );
    }
}
