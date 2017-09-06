<?php

namespace DmServer\Controllers\User;

use Dm\Models\Achats;
use Dm\Models\AuteursPseudos;
use Dm\Models\EmailsVentes;
use Dm\Models\Numeros;
use Dm\Models\Users;

use DmServer\Controllers\AbstractInternalController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalController extends AbstractInternalController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM, $function);
    }
    
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post('/internal/user/sale/{otherUser}', function (Request $request, Application $app, $otherUser) {
            return self::wrapInternalService($app, function(EntityManager $dmEm) use ($app, $otherUser) {
                $saleEmail = new EmailsVentes();

                $saleEmail->setUsernameVente(self::getSessionUser($app)['username']);
                $saleEmail->setUsernameAchat($otherUser);

                $dmEm->persist($saleEmail);
                $dmEm->flush();

                return new Response('OK');
            });
        });

        $routing->get('/internal/user/sale/{otherUser}/{date}', function (Request $request, Application $app, $otherUser, $date) {
            return self::wrapInternalService($app, function(EntityManager $dmEm) use ($app, $otherUser, $date) {
                $access = $dmEm->getRepository(EmailsVentes::class)->findBy([
                    'usernameVente' => self::getSessionUser($app)['username'],
                    'usernameAchat' => $otherUser,
                    'date' => \DateTime::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00')
                ]);

                return new JsonResponse(ModelHelper::getSerializedArray($access));
            });
        });
    }
}
