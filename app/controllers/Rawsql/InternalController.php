<?php

namespace DmServer\Controllers\Rawsql;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\QueryRedirect;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;

/**
 * @SLX\Controller
 */
class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, null, $function);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="/internal/rawsql")
     * )
     * @param Request     $request
     * @param Application $app
     * @return JsonResponse
     * @throws \InvalidArgumentException
     */
    public function runQuery(Request $request, Application $app) {
        return self::wrapInternalService($app, function() use ($request, $app) {
            $query = $request->request->get('query');
            $db = $request->request->get('db');
            $log = $request->request->get('log');
            $redirectTo = $request->request->get('redirect-to');
            $parameters = $request->request->get('parameters') ?: [];

            if (isset($redirectTo)) {
                $results = QueryRedirect::executeRemoteQuery($query, $parameters, $redirectTo);

                if (!(isset($log) && $log === 0)) {
                    $app['monolog']->addInfo("Raw sql sent remotely: $query with ".print_r($parameters, true));
                }
            }
            else {
                $em = DmServer::getEntityManager($db);
                if (is_null($em)) {
                    return new Response('Invalid parameter : db='.$db, Response::HTTP_BAD_REQUEST);
                }
                if (strpos($query, ';') !== false) { // In lack of something better
                    return new Response('Raw queries shouldn\'t contain the ";" symbol', Response::HTTP_BAD_REQUEST);
                }
                if (stripos(trim($query), 'SELECT') === 0) {
                    $results = $em->getConnection()->fetchAll($query, $parameters);
                }
                else {
                    $results = $em->getConnection()->executeQuery($query, $parameters);
                }

                if (!(isset($log) && $log === 0)) {
                    $app['monolog']->addInfo("Raw sql sent : $query");
                }
            }
            return new JsonResponse($results);
        });
    }
}
