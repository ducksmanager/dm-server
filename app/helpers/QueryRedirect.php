<?php
namespace DmServer;

use GuzzleHttp\Client;
use RuntimeException;

class QueryRedirect {

    public static $client;

    /**
     * @param string $query
     * @param array $params
     * @param string $redirectTo
     * @return array
     * @throws RuntimeException
     */
    public static function executeRemoteQuery($query, $params, $redirectTo) {
        if (DmServer::$settings['remote_query_name'] !== $redirectTo) {
            throw new \RuntimeException("Invalid redirection name : $redirectTo");
        }
        if (!isset(self::$client)) {
            self::$client = new Client([
                'base_uri' => DmServer::$settings['remote_query_server']
            ]);
        }

        $output = self::$client->get('/remote/sql.php', ['query' => [
            'db' => DmServer::$settings['remote_query_db'],
            'mdp' => sha1(DmServer::$settings['remote_query_password']),
            'req' => $query,
            'params' => json_encode($params)
        ]])->getBody()->getContents();

        $unserialized = unserialize($output);
        if (is_array($unserialized)) {
            [$fields,$results] = $unserialized;
            foreach($fields as $nom_champ) {
                foreach($results as $i=>$result) {
                    $results[$i][$nom_champ]=$result[$nom_champ];
                }
            }
            return $results;
        }
        return [];
    }
}