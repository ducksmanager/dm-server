<?php

namespace DmServer;

use Dm\Contracts\Dtos\NumeroSimple;
use Dm\Models\Numeros;
use Dm\Contracts\Results\FetchCollectionResult;
use Doctrine\Common\Collections\ArrayCollection;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends AppController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post(
            '/collection/new',
            function (Application $app, Request $request) {
                $check = self::callInternal($app, '/user/new/check', 'GET', [
                    $request->request->get('username'),
                    $request->request->get('password'),
                    $request->request->get('password2')
                ]);
                if ($check->getStatusCode() !== Response::HTTP_OK) {
                    return $check;
                }
                else {
                    return self::callInternal($app, '/user/new', 'PUT', [
                        'username' => $request->request->get('username'),
                        'password' => $request->request->get('password'),
                        'email' => $request->request->get('email')
                    ]);
                }

            }
        );

        $routing->post(
            '/collection/add',
            function (Application $app, Request $request) {
                return self::callInternal($app, '/collection/add', 'PUT', [
                    'country' => $request->request->get('country'),
                    'publication' => $request->request->get('publication'),
                    'issuenumber' => $request->request->get('issuenumber'),
                    'condition'   => $request->request->get('condition')
                ]);
            }
        );

        $routing->get(
            '/collection/fetch',
            function (Application $app, Request $request) {
                $issuesResponse = self::callInternal($app, '/collection/fetch', 'GET');
                if ($issuesResponse->getStatusCode() !== Response::HTTP_OK) {
                    return $issuesResponse;
                }
                /** @var Numeros[] $issues */
                $issues = ModelHelper::getUnserializedArrayFromJson($issuesResponse->getContent());

                $result = new FetchCollectionResult();
                foreach ($issues as $issue) {
                    $publicationCode = PublicationHelper::getPublicationCode($issue);
                    $numero = $issue->getNumero();
                    $etat = $issue->getEtat();

                    if (!$result->getNumeros()->containsKey($publicationCode)) {
                        $result->getNumeros()->set($publicationCode, new ArrayCollection());
                    }

                    $result->getNumeros()->get($publicationCode)->add(new NumeroSimple($numero, $etat));
                }

                $countryNames = ModelHelper::getUnserializedArrayFromJson(
                    self::callInternal($app, '/coa/countrynames', 'GET', [
                        implode(',', array_unique(
                            array_map(function (Numeros $issue) {
                                return $issue->getPays();
                            }, $issues)
                        ))
                    ])->getContent()
                );
                $result->getStatic()->setPays(new ArrayCollection($countryNames));

                $publicationTitles = ModelHelper::getUnserializedArrayFromJson(
                    self::callInternal($app, '/coa/publicationtitles', 'GET', [
                        implode(',', array_unique(
                            array_map(function (Numeros $issue) {
                                return PublicationHelper::getPublicationCode($issue);
                            }, $issues)
                        ))
                    ])->getContent()
                );
                $result->getStatic()->setMagazines(new ArrayCollection($publicationTitles));

                return new JsonResponse($result->toArray());
            }
        );
    }
}
