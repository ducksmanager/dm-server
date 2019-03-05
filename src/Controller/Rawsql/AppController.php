<?php

namespace App\Controller\Rawsql;

use App\Controller\AbstractController;
use App\Controller\RequiresDmVersionController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController implements RequiresDmVersionController
{
    /**
     * @Route(methods={"POST"}, path="/rawsql")
     * @throws \Doctrine\DBAL\DBALException
     */
    public function runQuery(Request $request, LoggerInterface $logger): Response
    {
        $query = $request->request->get('query');
        $db = preg_replace('#^db_#', '', $request->request->get('db'));
        $log = $request->request->get('log');
        $parameters = $request->request->get('parameters') ?: [];

        try {
            $em = $this->getEm($db);
        }
        catch(\InvalidArgumentException $e) {
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
            $logger->info("Raw sql sent: $query with ".print_r($parameters, true));
        }
        return new JsonResponse($results);
    }
}
