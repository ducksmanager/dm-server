<?php

namespace DmServer\Controllers\Collection;

use Dm\Contracts\Dtos\NumeroSimple;
use Dm\Contracts\Results\FetchCollectionResult;
use Dm\Models\Numeros;
use DmServer\Controllers\AbstractController;
use DmServer\ModelHelper;
use DmServer\PublicationHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Radebatz\Silex2Swagger\Swagger\Annotations as S2S;
use Swagger\Annotations as SWG;

/**
 * @S2S\Controller(prefix="/collection",
 *   @SWG\Parameter(
 *     name="x-dm-version",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Parameter(
 *     name="x-dm-user",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Parameter(
 *     name="x-dm-pass",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Response(response=200),
 *   @SWG\Response(response=401, description="User not authorized"),
 *   @SWG\Response(response="default", description="Error")
 * ),
 * @SLX\Before("DmServer\RequestInterceptor::checkVersion")
 * @SLX\Before("DmServer\RequestInterceptor::authenticateUser")
 */
class AppController extends AbstractController
{
    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="issues")
     * )
     * @param Application $app
     * @return JsonResponse
     */
    public function getIssues(Application $app)
    {
        $issuesResponse = self::callInternal($app, '/collection/issues', 'GET');

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

    /**
     * @SLX\Route(
     *   @SLX\Request(method="POST", uri="issues"),
     *   @SWG\Parameter(
     *     name="country",
     *     in="body",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="publication",
     *     in="body",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="issuenumbers",
     *     in="body",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="condition",
     *     in="body",
     *     required=false
     *   ),
     *	 @SLX\Value(variable="condition", default=null),
     *   @SWG\Parameter(
     *     name="istosell",
     *     in="body",
     *     required=false
     *   ),
     *	 @SLX\Value(variable="istosell", default=null),
     *   @SWG\Parameter(
     *     name="purchaseid",
     *     in="body",
     *     required=false
     *   ),
     *	 @SLX\Value(variable="purchaseid", default=null)
     * )
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     */
    public function postIssues(Application $app, Request $request) {
        $country = $request->request->get('country');
        $publication = $request->request->get('publication');
        $issuenumbers = $request->request->get('issuenumbers');
        $condition = $request->request->get('condition');
        $istosell = $request->request->get('istosell');
        $purchaseid = $request->request->get('purchaseid');

        if ($condition === 'non_possede') {
            return new JsonResponse(
                self::callInternal($app, '/collection/issues', 'DELETE', [
                    'country' => $country,
                    'publication' => $publication,
                    'issuenumbers' => $issuenumbers
                ])->getContent(), 200, [], true
            );
        } else {
            return new JsonResponse(
                self::callInternal($app, '/collection/issues', 'POST', [
                    'country' => $country,
                    'publication' => $publication,
                    'issuenumbers' => $issuenumbers,
                    'condition' => $condition,
                    'istosell' => $istosell,
                    'purchaseid' => $purchaseid
                ])->getContent(), 200, [], true
            );
        }
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="POST", uri="purchases/{purchaseid}"),
     *   @SWG\Parameter(
     *     name="purchaseid",
     *     in="path",
     *     required=false
     *   ),
     *	 @SLX\Value(variable="purchaseid", default=null)
     * )
     * @param Application $app
     * @param Request $request
     * @param string $purchaseid
     * @return Response
     */
    public function postPurchase(Application $app, Request $request, $purchaseid) {
        $response = self::callInternal(
            $app,
            self::buildUrl('/collection/purchases', is_null($purchaseid) ? [] : [$purchaseid]),
            'POST', [
                'date' => $request->request->get('date'),
                'description' => $request->request->get('description')
            ]
        );

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse($response->getContent(), 200, [], true);
        }
        else {
            return $response;
        }
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="PUT", uri="externalaccess")
     * )
     * @param Application $app
     * @return Response
     */
    public function addExternalAccess (Application $app) {
        return self::callInternal($app, '/collection/externalaccess', 'PUT');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="externalaccess/{key}"),
     *   @SWG\Parameter(
     *     name="key",
     *     in="path",
     *     required=true
     *   )
     * )
     * @param Application $app
     * @param string $key
     * @return Response
     */
    public function getExternalAccess(Application $app, $key) {
        return self::callInternal($app, "/collection/externalaccess/$key");
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="bookcase/sort")
     * )
     * @param Application $app
     * @return Response
     */
    public function getBookcaseSorting(Application $app) {
        return self::callInternal($app, '/collection/bookcase/sort');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="bookcase/sort/max")
     * )
     * @param Application $app
     * @return Response
     */
    public function getLastPublicationPosition(Application $app) {
        return self::callInternal($app, '/collection/bookcase/sort/max');
    }
}
