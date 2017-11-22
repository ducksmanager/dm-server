<?php

namespace DmServer\Controllers\Ducksmanager;

use Dm\Contracts\Results\EventResult;
use Dm\Models\Achats;
use Dm\Models\AuteursPseudos;

use Dm\Models\Bouquineries;
use Dm\Models\Numeros;
use Dm\Models\TranchesPretes;
use Dm\Models\TranchesPretesContributeurs;
use Dm\Models\Users;

use DmServer\Controllers\AbstractInternalController;
use DmServer\DmServer;

use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Silex\Application;

use Swift_Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;

/**
 * @SLX\Controller(prefix="/internal/ducksmanager")
 */
class InternalController extends AbstractInternalController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM, $function);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="exists/{username}"),
     * )
     * @param Application $app
     * @param string $username
     * @return Response
     */
    public function getUsernameExists(Application $app, $username) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($username) {
            $existingUser = $dmEm->getRepository(Users::class)->findBy([
                'username' => $username
            ]);
            if (count($existingUser) > 0) {
                return new Response('', Response::HTTP_OK);
            }
            return new Response('', Response::HTTP_NO_CONTENT);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="new/check/{username}/{password}/{password2}"),
     * )
     * @param Application $app
     * @param string $username
     * @param string $password
     * @param string $password2
     * @return Response
     */
    public function checkNewUser(Application $app, $username, $password, $password2) {
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
                if (self::callInternal($app, '/ducksmanager/exists', 'GET', [$username])
                        ->getStatusCode() !== Response::HTTP_NO_CONTENT) {
                    return new Response(self::$translator->trans('UTILISATEUR_EXISTANT'), Response::HTTP_CONFLICT);
                }
            }
        }
        if (is_null($error)) {
            return new Response('OK', Response::HTTP_OK);
        }

        return new Response(self::$translator->trans($error), Response::HTTP_PRECONDITION_FAILED);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="check/{username}/{password}"),
     * )
     * @param Application $app
     * @param string $username
     * @param string $password
     * @return Response
     */
    public function checkExistingUser(Application $app, $username, $password) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($username, $password) {
            /** @var Users $existingUser */
            $existingUser = $dmEm->getRepository(Users::class)->findOneBy([
                'username' => $username,
                'password' => $password
            ]);
            if (!is_null($existingUser)) {
                return new Response($existingUser->getId(), Response::HTTP_OK);
            } else {
                return new Response('', Response::HTTP_UNAUTHORIZED);
            }
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="new"),
     * )
     * @param Request $request
     * @param Application $app
     * @return Response
     */
    public function createUser(Request $request, Application $app) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($request) {
            $user = new Users();
            $user->setUsername($request->request->get('username'));
            $user->setPassword(sha1($request->request->get('password')));
            $user->setEmail($request->request->get('email'));
            $user->setDateinscription(new \DateTime());

            $dmEm->persist($user);
            $dmEm->flush();

            return new Response('OK', Response::HTTP_CREATED);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="DELETE", uri="{userId}/data"),
     * )
     * @param Application $app
     * @param string $userId
     * @return Response
     */
    public function deleteUserData(Application $app, $userId) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($userId) {
            $qb = $dmEm->createQueryBuilder();

            $qb->delete(Numeros::class, 'issues')
                ->where($qb->expr()->eq('issues.idUtilisateur', ':userId'))
                ->setParameter(':userId', $userId);
            $qb->getQuery()->execute();

            $qb = $dmEm->createQueryBuilder();
            $qb->delete(Achats::class, 'purchases')
                ->where($qb->expr()->eq('purchases.idUser', ':userId'))
                ->setParameter(':userId', $userId);
            $qb->getQuery()->execute();

            $qb = $dmEm->createQueryBuilder();
            $qb->delete(AuteursPseudos::class, 'authorsUsers')
                ->where($qb->expr()->eq('authorsUsers.idUser', ':userId'))
                ->setParameter(':userId', $userId);
            $qb->getQuery()->execute();

            return new Response('OK');
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="{userId}/data/bookcase/reset"),
     * )
     * @param Application $app
     * @param string $userId
     * @return Response
     */
    public function resetBookcaseOptions(Application $app, $userId) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($userId) {
            $user = $dmEm->getRepository(Users::class)->findOneBy([
                'id' => $userId
            ]);

            $user->setBibliothequeTexture1('bois');
            $user->setBibliothequeSousTexture1('HONDURAS MAHOGANY');
            $user->setBibliothequeTexture2('bois');
            $user->setBibliothequeSousTexture2('KNOTTY PINE');
            $user->setBibliothequeGrossissement(1.5);

            $dmEm->persist($user);
            $dmEm->flush();

            return new Response('OK');
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="email/bookstore")
     * )
     * @param Application $app
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function sendBookstoreEmail(Application $app, Request $request) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($app, $request) {
            $userId = $request->request->get('userId');
            if (is_null($userId)) {
                $userName = 'anonymous';
            }
            else {
                $userName = $dmEm->getRepository(Users::class)->findOneBy([
                    'id' => $userId
                ])->getUsername();
            }

            $message = new \Swift_Message();
            $message
                ->setSubject('Ajout de bouquinerie')
                ->setFrom([$userName."@".DmServer::$settings['smtp_origin_email_domain_ducksmanager']])
                ->setTo([DmServer::$settings['smtp_username']])
                ->setBody('<a href="https://www.ducksmanager.net/backend/bouquineries.php">Validation</a>', 'text/html');

            /** @var Swift_Mailer $mailer */
            $mailer = $app['mailer'];
            $failures = [];
            // Pass a variable name to the send() method
            if (!$mailer->send($message, $failures)) {
                throw new \Exception("Can't send e-mail '$message': failed with ".print_r($failures, true));
            }

            return new Response(Response::HTTP_OK);
        });
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="recentevents/signups")
     * )
     * @param Application $app
     * @return Response
     * @throws \Exception
     */
    public function getSignupEvents(Application $app) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) {
            $qb = $dmEm->createQueryBuilder();
            $qb->select('users.id, (UNIX_TIMESTAMP(CURRENT_TIMESTAMP()) - UNIX_TIMESTAMP(users.dateinscription)) AS seconds_diff')
                ->from(Users::class, 'users')
                ->where($qb->expr()
                    ->andX(
                        $qb->expr()->exists(
                            $dmEm->createQueryBuilder()
                                ->select('1')
                                ->from(Numeros::class, 'issues')
                                ->where('users.id = issues.idUtilisateur')
                                ->getDQL()
                        ),
                        $qb->expr()->gt('users.dateinscription', 'DATE_SUB(CURRENT_TIMESTAMP(), 1, \'month\')'),
                        $qb->expr()->notLike('users.username', ':testUser')
                    ));
            $qb->setParameter(':testUser', 'test%');
            $results = $qb->getQuery()->getArrayResult();

            $signupEvents = array_map(
                function($event) {
                    return new EventResult('signup', $event['seconds_diff'], [$event['id']]);
                },
                $results
            );
            return new JsonResponse(ModelHelper::getSimpleArray($signupEvents));
        });
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="recentevents/collectioninserts")
     * )
     * @param Application $app
     * @return Response
     * @throws \Exception
     */
    public function getCollectionInsertEvents(Application $app) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) {
            $qb = $dmEm->createQueryBuilder();
            $qb->select(
                'users.id, ' .
                '(UNIX_TIMESTAMP(CURRENT_TIMESTAMP()) - UNIX_TIMESTAMP(issues.dateajout)) AS seconds_diff, ' .
                'DATE(issues.dateajout) AS insertdate, ' .
                'COUNT(issues.numero) AS issuecount')
                ->addSelect(
                    '('
                    .$dmEm->createQueryBuilder()
                        ->select(new Func('CONCAT', ['n.pays', $qb->expr()->literal('/'), 'n.magazine', $qb->expr()->literal(' '), 'n.numero']))
                        ->from(Numeros::class, 'n')
                        ->where('n.id = issues.id')
                        ->setMaxResults(1)
                    .') as example_issue'
                )
                ->from(Users::class, 'users')
                ->join(Numeros::class, 'issues', Join::WITH, 'users.id = issues.idUtilisateur')
                ->where($qb->expr()
                    ->andX(
                        $qb->expr()->gt('issues.dateajout', 'DATE_SUB(CURRENT_TIMESTAMP(), 1, \'month\')'),
                        $qb->expr()->notLike('users.username', ':testUser'),
                        $qb->expr()->notLike('users.username', ':demoUser')
                    ))
                ->groupBy('users.id, insertdate')
                ->having('count(issues.numero) > 0')
                ->orderBy('issues.dateajout', 'DESC');
            $qb->setParameter(':testUser', 'test%');
            $qb->setParameter(':demoUser', 'demo');
            $results = $qb->getQuery()->getArrayResult();

            $collectionInsertsEvents = array_map(
                function($event) {
                    preg_match('#^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)(?P<issuenumber_regex>[-A-Z0-9 ]+)$#', $event['example_issue'], $exampleIssueMatch);
                    return new EventResult('collectioninsert', $event['seconds_diff'], [$event['id']], [
                        'example_issue_publicationcode' => $exampleIssueMatch['publicationcode_regex'],
                        'example_issue_issuenumber' => $exampleIssueMatch['issuenumber_regex'],
                        'count' => $event['issuecount']
                    ]);
                },
                $results
            );
            return new JsonResponse(ModelHelper::getSimpleArray($collectionInsertsEvents));
        });
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="recentevents/bookstorecreations")
     * )
     * @param Application $app
     * @return Response
     * @throws \Exception
     */
    public function getBookstoreCreationEvents(Application $app) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) {
            $qb = $dmEm->createQueryBuilder();

            $qb->select(
                'bookstores.idUtilisateur AS id, ' .
                '(UNIX_TIMESTAMP(CURRENT_TIMESTAMP()) - UNIX_TIMESTAMP(bookstores.dateajout)) AS seconds_diff, ' .
                'bookstores.nom AS name')
                ->from(Bouquineries::class, 'bookstores')
                ->where($qb->expr()
                    ->andX(
                        'bookstores.actif=1',
                        $qb->expr()->gt('bookstores.dateajout', 'DATE_SUB(CURRENT_TIMESTAMP(), 1, \'month\')')
                    )
                );
            $results = $qb->getQuery()->getArrayResult();

            $bookstoreCreationEvents = array_map(
                function ($event) {
                    return new EventResult('bookstorecreation', $event['seconds_diff'], [$event['id']], [
                        'name' => $event['name']
                    ]);
                },
                $results
            );

            return new JsonResponse(ModelHelper::getSimpleArray($bookstoreCreationEvents));
        });
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="recentevents/creatededges")
     * )
     * @param Application $app
     * @return Response
     * @throws \Exception
     */
    public function getCreatedEdgeEvents(Application $app) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) {
            $qb = $dmEm->createQueryBuilder();

            $ignoredEdges = [
                'fr/JM' => '%-%'
            ];
            $ignoredEdgesFilter = [];

            array_walk($ignoredEdges, function($regex, $publicationCode) use(&$qb, &$ignoredEdgesFilter) {
                $publicationCodeClean = str_replace('/', '', $publicationCode);
                $publicationCodeParamer = ":ignoredEdgesFor${publicationCodeClean}_Code";
                $regexParameter = ":ignoredEdgesFor${publicationCodeClean}_Regex";
                $qb->setParameter($publicationCodeParamer, $publicationCode);
                $qb->setParameter($regexParameter, $regex);
                $ignoredEdgesFilter[]=$qb->expr()->not($qb->expr()->andX(
                    $qb->expr()->like('edges.publicationcode', $publicationCodeParamer),
                    $qb->expr()->like('edges.issuenumber', $regexParameter)
                ));
            });

            $ignoredEdgesExpression = call_user_func_array([$qb->expr(), 'andX'], $ignoredEdgesFilter);

//            $rsm = new ResultSetMappingBuilder($dmEm);
//            $rsm->addScalarResult('dateajout', $alias);
//
//            $dmEm->createNativeQuery("
//                SELECT
//                  DATE(t1_.dateajout)                                                      AS dateajout,
//                  contributeurs.contributeurs                                              AS contributeurs,
//                  GROUP_CONCAT(DISTINCT concat(t1_.publicationcode, ' ', t1_.issuenumber)) AS issuenumber_4
//                FROM tranches_pretes t1_
//                  INNER JOIN (
//                               SELECT
//                                 t12_.dateajout,
//                                 t12_.publicationcode,
//                                 t12_.issuenumber,
//                                 GROUP_CONCAT(DISTINCT t02_.contributeur) AS contributeurs
//                               FROM tranches_pretes t12_
//                                 INNER JOIN tranches_pretes_contributeurs t02_
//                                   ON (t12_.publicationcode = t02_.publicationcode AND t12_.issuenumber = t02_.issuenumber)
//                               GROUP BY t12_.dateajout, t12_.publicationcode, t12_.issuenumber
//                             ) AS contributeurs
//                    ON (t1_.dateajout = contributeurs.dateajout AND t1_.publicationcode = contributeurs.publicationcode AND
//                        t1_.issuenumber = contributeurs.issuenumber)
//                WHERE
//                  (NOT (t1_.publicationcode LIKE 'fr/JM' AND t1_.issuenumber LIKE '%-%')) AND
//                  t1_.dateajout > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 6 MONTH)
//                GROUP BY DATE(t1_.dateajout), contributeurs.contributeurs
//                ORDER BY t1_.dateajout DESC;
//            ");

            $subqueryContributors = $dmEm->createQueryBuilder();
            $subqueryContributors
                ->select(
                    'GROUP_CONCAT(DISTINCT contributors.contributeur)')
                ->from(TranchesPretes::class, 'edges2')
                ->join(TranchesPretesContributeurs::class, 'contributors', Join::WITH, 'edges2.publicationcode = contributors.publicationcode AND edges2.issuenumber = contributors.issuenumber')
                ->where($subqueryContributors->expr()
                    ->andX(
                        $subqueryContributors->expr()->eq('edges.dateajout', 'edges2.dateajout'),
                        $subqueryContributors->expr()->eq('edges.publicationcode', 'edges2.publicationcode'),
                        $subqueryContributors->expr()->eq('edges.issuenumber', 'edges2.issuenumber')
                    ));

            $qb
                ->addSelect('('.$subqueryContributors->getDQL().') AS ids')
                ->select(
                    'GROUP_CONCAT(DISTINCT contributors.contributeur) AS ids, ' .
                    '(UNIX_TIMESTAMP(CURRENT_TIMESTAMP()) - UNIX_TIMESTAMP(edges.dateajout)) AS seconds_diff, ' .
                    'DATE(edges.dateajout) AS insertdate')
                ->from(TranchesPretes::class, 'edges')
                ->join(TranchesPretesContributeurs::class, 'contributors', Join::WITH, 'edges.publicationcode = contributors.publicationcode AND edges.issuenumber = contributors.issuenumber')
                ->where($qb->expr()
                    ->andX(
                        $ignoredEdgesExpression,
                        $qb->expr()->gt('edges.dateajout', 'DATE_SUB(CURRENT_TIMESTAMP(), 1, \'month\')')
                    )
                )
                ->groupBy('insertdate, contributors.contributors')
                ->orderBy('edges.dateajout', 'DESC');

            $dql = $qb->getQuery()->getDQL();
            $sql = $qb->getQuery()->getSQL();
            $results = $qb->getQuery()->getArrayResult();

            $results = array_filter($results, function($result) {
                return !is_null($result['ids']);
            });

            $edgeEvents = array_map(
                function ($event) {
                    return new EventResult('edgecreation', $event['seconds_diff'], array_map('intval', explode(',', $event['ids'])), [
                        'publicationcode' => $event['publicationcode'],
                        'issuenumber' => $event['issuenumber'],
                    ]);
                },
                $results
            );

            return new JsonResponse(ModelHelper::getSimpleArray($edgeEvents));
        });
    }
}
