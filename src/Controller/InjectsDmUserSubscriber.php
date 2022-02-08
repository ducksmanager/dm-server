<?php

namespace App\Controller;

use App\Entity\Dm\Users;
use App\Entity\Dm\UsersPermissions;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class InjectsDmUserSubscriber implements EventSubscriberInterface
{
    private ObjectManager $dmEm;
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger) {
        $this->dmEm = $registry->getManager('dm');
        $this->logger = $logger;
    }

    public function onKernelRequest(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $controller = $event->getController();

        $username = utf8_encode($event->getRequest()->headers->get('x-dm-user'));
        $password = $event->getRequest()->headers->get('x-dm-pass');
        if (!empty($username) && !empty($password)) {
            $this->logger->info("Authenticating $username...");

            $existingUser = $this->dmEm->getConnection()->fetchAssociative(
                "SELECT ID, username FROM users WHERE username = CONVERT(? USING utf8mb4) AND password=?",
                [$username, $password]
            );

            if ($existingUser === false) {
                if (self::isUserRequired($controller)) {
                    throw new UnauthorizedHttpException('Invalid credentials!');
                }
            }
            else {
                if (self::isAdminUserRequired($controller)
                 && null === $this->dmEm->getRepository(UsersPermissions::class)->findOneBy([
                        'username' => $username,
                        'role' => 'EdgeCreator',
                        'privilege' => 'Admin'
                    ])) {
                    throw new HttpException(403, 'You need admin rights!');
                }
                $request->getSession()->set('user', [
                    'username' => $existingUser['username'],
                    'id' => $existingUser['ID']
                ]);
                $this->logger->info("$username is logged in");
            }
        }
        else if (self::isUserRequired($controller)) {
            throw new UnauthorizedHttpException('Credentials are required!');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onKernelRequest',
        ];
    }

    private static function isUserRequired(callable $controller): bool {
        return is_array($controller) && (
                $controller[0] instanceof InjectsDmUserController
             || $controller[0] instanceof RequiresAdminEdgeCreatorController);
    }

    private static function isAdminUserRequired(callable $controller): bool
    {
        return is_array($controller) && $controller[0] instanceof RequiresAdminEdgeCreatorController;
    }
}
