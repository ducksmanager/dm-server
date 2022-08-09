<?php

namespace App\Controller;

use Doctrine\DBAL\DBALException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RawsqlController extends AbstractController implements RequiresDmVersionController
{
    /**
     * @Route(methods={"POST"}, path="/rawsql")
     * @throws DBALException
     */
    public function runQuery(Request $request, LoggerInterface $logger): Response
    {
        $query = $request->request->get('query');
        $db = preg_replace('#^db_#', '', $request->request->get('db'));
        $log = $request->request->get('log');
        $parameters = $request->request->get('parameters') ?: [];
        if (is_string($parameters)) {
            $parameters = json_decode($parameters, true);
        }

        try {
            $em = $this->getEm($db);
        }
        catch(InvalidArgumentException $e) {
            return new Response('Invalid parameter : db='.$db, Response::HTTP_BAD_REQUEST);
        }
        if (strpos($query, ';') !== false) { // In lack of something better
            return new Response('Raw queries shouldn\'t contain the ";" symbol', Response::HTTP_BAD_REQUEST);
        }

        if (!(isset($log) && $log === 0)) {
            $logger->info("Raw sql sent: $query with ".print_r($parameters, true));
        }

        if (stripos(trim($query), 'SELECT') === 0) {
            $results = $em->getConnection()->fetchAll($query, $parameters);
        }
        else {
            $results = $em->getConnection()->executeQuery($query, $parameters);
        }
        return new JsonResponse($results);
    }
}
