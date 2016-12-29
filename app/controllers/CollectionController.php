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
        $routing->get(
            '/collection/issues',
            function (Application $app, Request $request) {
                $issuesResponse = self::callInternal($app, '/collection/issues', 'GET');
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

        $routing->post(
            '/collection/issues',
            function (Application $app, Request $request) {
                $country = $request->request->get('country');
                $publication = $request->request->get('publication');
                $issuenumbers = $request->request->get('issuenumbers');
                $condition = $request->request->get('condition');
                $istosell = $request->request->get('istosell');
                $purchaseid = $request->request->get('purchaseid');

                if ($condition === 'non_possede') {
                    return new JsonResponse(
                        self::callInternal($app, '/collection/issues', 'DELETE', [
                            'country'      => $country,
                            'publication'  => $publication,
                            'issuenumbers' => $issuenumbers
                        ])->getContent(), 200, [], true
                    );
                }
                else {
                    return new JsonResponse(
                        self::callInternal($app, '/collection/issues', 'POST', [
                            'country'      => $country,
                            'publication'  => $publication,
                            'issuenumbers' => $issuenumbers,
                            'condition' => $condition,
                            'istosell' => $istosell,
                            'purchaseid' => $purchaseid
                        ])->getContent(), 200, [], true
                    );
                }
            }
        )
            ->value('condition', null)
            ->value('istosell', null)
            ->value('purchaseid', null);
    }
}
