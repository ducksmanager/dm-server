<?php

namespace DmServer\Controllers\User;

use DmServer\Controllers\AbstractController;
use DmServer\CsvHelper;
use DmServer\DmServer;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post(
            '/user/new',
            function (Application $app, Request $request) {
                $check = self::callInternal($app, '/user/new/check', 'GET', [
                    $request->request->get('username'),
                    $request->request->get('password'),
                    $request->request->get('password2')
                ]);
                if ($check->getStatusCode() !== Response::HTTP_OK) {
                    return $check;
                } else {
                    return self::callInternal($app, '/user/new', 'PUT', [
                        'username' => $request->request->get('username'),
                        'password' => $request->request->get('password'),
                        'email' => $request->request->get('email')
                    ]);
                }

            }
        );

        $routing->post(
            '/user/resetDemo',
            function (Application $app, Request $request) {
                $demoUserResponse = self::callInternal($app, '/rawsql', 'POST', [
                    'query' => 'SELECT ID FROM users WHERE username=\'demo\'',
                    'db' => DmServer::CONFIG_DB_KEY_DM
                ]);

                if ($demoUserResponse->getStatusCode() === Response::HTTP_OK) {
                    $demoUserData = json_decode($demoUserResponse->getContent());
                    if (!is_null($demoUserData) && count($demoUserData) > 0) {
                        $demoUserId = $demoUserData[0]->ID;
                        $dataDeleteResponse = self::callInternal($app, '/user/' . $demoUserId . '/data', 'DELETE');
                        if ($dataDeleteResponse->getStatusCode() === Response::HTTP_OK) {
                            $bookcaseOptionsResetResponse = self::callInternal($app, '/user/' . $demoUserId . '/data/bookcase/reset', 'POST');

                            if ($bookcaseOptionsResetResponse->getStatusCode() === Response::HTTP_OK) {
                                self::setSessionUser($app, 'demo', $demoUserId);

                                $demoUserIssueData = CsvHelper::readCsv(implode(DIRECTORY_SEPARATOR, [getcwd(), 'assets', 'demo_user', 'issues.csv']));

                                foreach ($demoUserIssueData as $publicationData) {
                                    $response = self::callInternal($app, '/collection/issues', 'POST', $publicationData);

                                    if ($response->getStatusCode() !== Response::HTTP_OK) {
                                        return $response;
                                    }
                                }

                                $demoUserPurchaseData = CsvHelper::readCsv(implode(DIRECTORY_SEPARATOR, [getcwd(), 'assets', 'demo_user', 'purchases.csv']));

                                foreach ($demoUserPurchaseData as $purchaseData) {
                                    $response = self::callInternal($app, '/collection/purchases', 'POST', $purchaseData);

                                    if ($response->getStatusCode() !== Response::HTTP_OK) {
                                        return $response;
                                    }
                                }
                            }
                            return $bookcaseOptionsResetResponse;
                        } else {
                            return $dataDeleteResponse;
                        }
                    }
                    else {
                        return new Response('Malformed demo user data or no data user', Response::HTTP_EXPECTATION_FAILED);
                    }
                }
                else {
                    return $demoUserResponse;
                }
            }
        );

        $routing->post(
            '/user/sale/{otheruser}',
            function (Application $app, Request $request, $otheruser) {

                if (self::callInternal($app, '/user/exists', 'GET', [$otheruser])->getStatusCode() === Response::HTTP_NO_CONTENT) {
                    return new Response(self::$translator->trans('UTILISATEUR_INVALIDE'), Response::HTTP_BAD_REQUEST);
                }

                return self::callInternal($app, "/user/sale/$otheruser", 'POST');
            }
        );

        $routing->get(
            '/user/sale/{otheruser}/{date}',
            function (Application $app, Request $request, $otheruser, $date) {
                return self::callInternal($app, "/user/sale/$otheruser/$date", 'GET');
            }
        );
    }
}
