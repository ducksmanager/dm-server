<?php

namespace Wtd;

use Coa\Models\InducksCountryname;
use Coa\Models\InducksIssue;
use Coa\Models\InducksPublication;
use Exception;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
                try {
                    $existingUser = Wtd::getDmEntityManager()->getRepository(Users::class)->findBy(array(
                        'username' => $username
                    ));
                    if (count($existingUser) > 0) {
                        return new Response('', Response::HTTP_CONFLICT);
                    }
                    return new Response('', Response::HTTP_OK);
                }
                catch (Exception $e) {
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
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
                try {
                    $existingUser = Wtd::getDmEntityManager()->getRepository(Users::class)->findBy(array(
                        'username' => $username,
                        'password' => sha1($password)
                    ));
                    if (count($existingUser) > 0) {
                        return new Response($existingUser[0]->getId(), Response::HTTP_OK);
                    }
                    else {
                        return new Response('', Response::HTTP_UNAUTHORIZED);
                    }
                }
                catch (Exception $e) {
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        );

        $routing->put('/internal/user/new', function (Request $request, Application $app) {
            $user = new Users();
            $user->setUsername($request->request->get('username'));
            $user->setPassword(sha1($request->request->get('password')));
            $user->setEmail($request->request->get('email'));
            $user->setDateinscription(new \DateTime());

            try {
                Wtd::getDmEntityManager()->persist($user);
                Wtd::getDmEntityManager()->flush();
            }
            catch (Exception $e) {
                return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new Response('OK', Response::HTTP_CREATED);
        });

        $routing->put('/internal/collection/add', function (Request $request, Application $app) {
            $issue = new Numeros();
            $issue->setPays($request->request->get('country'));
            $issue->setMagazine($request->request->get('publication'));
            $issue->setNumero($request->request->get('issuenumber'));
            $issue->setEtat($request->request->get('condition'));
            $issue->setIdUtilisateur(self::getSessionUser($app)['id']);

            try {
                Wtd::getDmEntityManager()->persist($issue);
                Wtd::getDmEntityManager()->flush();
            }
            catch (Exception $e) {
                return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new Response('OK', Response::HTTP_CREATED);
        });

        $routing->get(
            '/internal/collection/fetch',
            function (Request $request, Application $app) {
                try {
                    /** @var Numeros[] $issues */
                    $issues = Wtd::getDmEntityManager()->getRepository(Numeros::class)->findBy(
                        ['idUtilisateur' => self::getSessionUser($app)['id']],
                        ['pays' => 'asc', 'magazine' => 'asc', 'numero' => 'asc']
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issues));
                }
                catch (Exception $e) {
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        );

        $routing->get(
            '/internal/coa/countrynames/{countryCodes}',
            function (Request $request, Application $app, $countryCodes) {
                try {
                    $qb = Wtd::getCoaEntityManager()->createQueryBuilder();
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
                }
                catch (Exception $e) {
                    new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        )->value('countryCodes', null);

        $routing->get(
            '/internal/coa/publicationtitles/{publicationCodes}',
            function (Request $request, Application $app, $publicationCodes) {
                try {
                    $qb = Wtd::getCoaEntityManager()->createQueryBuilder();
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
                }
                catch (Exception $e) {
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        )->assert('publicationCodes', '.+');

        $routing->get(
            '/internal/coa/issues/{publicationCode}',
            function (Request $request, Application $app, $publicationCode) {
                try {
                    $qb = Wtd::getCoaEntityManager()->createQueryBuilder();
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
                }
                catch (Exception $e) {
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        )->assert('publicationCode', '.+');
    }
}
