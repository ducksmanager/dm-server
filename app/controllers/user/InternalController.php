<?php

namespace DmServer\Controllers\User;

use Dm\Models\EmailsVentes;


use DmServer\Controllers\AbstractInternalController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;

/**
 * @SLX\Controller(prefix="/internal/user")
 */
class InternalController extends AbstractInternalController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM, $function);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="sale/{otherUser}"),
     * )
     * @param Application $app
     * @param string $otherUser
     * @return JsonResponse
     */
    function sellToUser(Application $app, $otherUser) {
        return self::wrapInternalService($app, function (EntityManager $dmEm) use ($app, $otherUser) {
            $saleEmail = new EmailsVentes();

            $saleEmail->setUsernameVente(self::getSessionUser($app)['username']);
            $saleEmail->setUsernameAchat($otherUser);

            $dmEm->persist($saleEmail);
            $dmEm->flush();

            return new Response('OK');
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="sale/{otherUser}/{date}"),
     * )
     * @param Application $app
     * @param string $otherUser
     * @param string $date
     * @return JsonResponse
     */
    function getSaleToUserAtDate(Application $app, $otherUser, $date) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($app, $otherUser, $date) {
            $access = $dmEm->getRepository(EmailsVentes::class)->findBy([
                'usernameVente' => self::getSessionUser($app)['username'],
                'usernameAchat' => $otherUser,
                'date' => \DateTime::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00')
            ]);

            return new JsonResponse(ModelHelper::getSerializedArray($access));
        });
    }
}
