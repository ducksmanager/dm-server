<?php

namespace Wtd;

use Coa\Models\InducksCountryname;
use Coa\Models\InducksIssue;
use Coa\Models\InducksPublication;
use CoverId\Models\Covers;
use Silex\Application;
use Doctrine\ORM\Query\Expr\Join;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wtd\models\Coa\Contracts\Results\SimpleIssue;
use Wtd\Models\Numeros;
use Wtd\Models\Users;

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
                return AppController::return500ErrorOnException(function() use ($username) {
                    $existingUser = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(array(
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
                            return new Response(self::translate($app, 'UTILISATEUR_EXISTANT'), Response::HTTP_CONFLICT);
                        }
                    }
                }
                if (is_null($error)) {
                    return new Response('OK', Response::HTTP_OK);
                }

                return new Response(self::translate($app, $error), Response::HTTP_PRECONDITION_FAILED);
            }
        );

        $routing->get(
            '/internal/user/check/{username}/{password}',
            function (Request $request, Application $app, $username, $password) {
                return AppController::return500ErrorOnException(function() use ($username, $password) {
                    $existingUser = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(array(
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
            return AppController::return500ErrorOnException(function() use ($request) {
                $user = new Users();
                $user->setUsername($request->request->get('username'));
                $user->setPassword(sha1($request->request->get('password')));
                $user->setEmail($request->request->get('email'));
                $user->setDateinscription(new \DateTime());

                Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_DM)->persist($user);
                Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_DM)->flush();

                return new Response('OK', Response::HTTP_CREATED);
            });
        });

        $routing->put('/internal/collection/add', function (Request $request, Application $app) {
            return AppController::return500ErrorOnException(function() use ($request, $app) {
                $issue = new Numeros();
                $issue->setPays($request->request->get('country'));
                $issue->setMagazine($request->request->get('publication'));
                $issue->setNumero($request->request->get('issuenumber'));
                $issue->setEtat($request->request->get('condition'));
                $issue->setIdUtilisateur(self::getSessionUser($app)['id']);

                Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_DM)->persist($issue);
                Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_DM)->flush();

                return new Response('OK', Response::HTTP_CREATED);
            });
        });

        $routing->get(
            '/internal/collection/fetch',
            function (Request $request, Application $app) {
                return AppController::return500ErrorOnException(function() use ($app) {
                    /** @var Numeros[] $issues */
                    $issues = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_DM)->getRepository(Numeros::class)->findBy(
                        ['idUtilisateur' => self::getSessionUser($app)['id']],
                        ['pays' => 'asc', 'magazine' => 'asc', 'numero' => 'asc']
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issues));
                });
            }
        );

        $routing->get(
            '/internal/coa/countrynames/{countryCodes}',
            function (Request $request, Application $app, $countryCodes) {
                return AppController::return500ErrorOnException(function() use ($countryCodes) {
                    $qb = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_COA)->createQueryBuilder();
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
                return AppController::return500ErrorOnException(function() use ($publicationCodes) {
                    $qb = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_COA)->createQueryBuilder();
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
                return AppController::return500ErrorOnException(function() use ($publicationCode) {
                    $qb = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_COA)->createQueryBuilder();
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
            '/internal/coa/issuesbycodes/{issuecodes}',
            function (Request $request, Application $app, $issuecodes) {
                return AppController::return500ErrorOnException(function() use ($issuecodes) {
                    $issuecodesList = explode(',', $issuecodes);

                    $qb = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qb
                        ->select('inducks_publication.countrycode, inducks_publication.title, inducks_issue.issuenumber')
                        ->from(InducksIssue::class, 'inducks_issue')
                        ->join(InducksPublication::class, 'inducks_publication', Join::WITH, 'inducks_issue.publicationcode = inducks_publication.publicationcode');

                    $qb->where($qb->expr()->in('inducks_issue.issuecode', $issuecodesList));

                    $results = $qb->getQuery()->getResult();

                    array_walk(
                        $results,
                        function($issue, $i) use ($issuecodesList, &$issues) {
                            $issues[$issuecodesList[$i]] = new SimpleIssue($issue['countrycode'], $issue['title'], $issue['issuenumber']);
                        }
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issues));
                });
            }
        )->assert('issuecodes', '^([a-z]+/[- A-Z0-9]+,){0,4}[a-z]+/[- A-Z0-9]+$');

        $routing->get(
            '/internal/cover-id/issuecodes/{coverids}',
            function (Request $request, Application $app, $coverids) {
                return AppController::return500ErrorOnException(function() use ($coverids) {
                    $coveridsList = explode(',', $coverids);

                    $qb = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_COVER_ID)->createQueryBuilder();
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
            '/internal/cover-id/issue/{issueNumber}',
            function (Request $request, Application $app, $issueNumber) {
                return AppController::return500ErrorOnException(function() use ($issueNumber) {
                    $qb = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_COVER_ID)->createQueryBuilder();
                    $qb
                        ->select('covers.url, covers.id')
                        ->from(Covers::class, 'covers');

                    $qb->where($qb->expr()->eq('covers.issuecode', "'".$issueNumber."'"));

                    $result = $qb->getQuery()->getOneOrNullResult();

                    $localFilePath = Wtd::$settings['image_local_root'] . basename($result['url']);

                    file_put_contents(
                        $localFilePath,
                        file_get_contents(Wtd::$settings['image_remote_root'] . $result['url'])
                    );

                    return new BinaryFileResponse($localFilePath);
                });
            }
        )->assert('issueNumber', '^[a-z]+/[- A-Z0-9]+$');

        $routing->post(
            '/internal/rawsql',
            function (Request $request, Application $app) {
                return AppController::return500ErrorOnException(function() use ($request) {
                    $query = $request->request->get('query');
                    $db = $request->request->get('db');

                    $em = Wtd::getEntityManager($db);
                    if (is_null($em)) {
                        return new Response('Invalid parameter : db='.$db, Response::HTTP_BAD_REQUEST);
                    }

                    $results = $em->getConnection()->fetchAll($query);
                    return new JsonResponse($results);
                });
            }
        );
    }
}
