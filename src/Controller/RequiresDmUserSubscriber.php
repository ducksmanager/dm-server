<?php

namespace App\Controller;

use App\Entity\Dm\Users;
use App\Entity\Dm\UsersPermissions;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RequiresDmUserSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface $dmEm
     */
    private $dmEm;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger) {
        $this->dmEm = $registry->getManager('dm');
        $this->logger = $logger;
    }

    public function onKernelRequest(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $controller = $event->getController();

        if (is_array($controller) && (
            $controller[0] instanceof RequiresDmUserController
         || $controller[0] instanceof RequiresAdminEdgeCreatorController)) {
            $username = $event->getRequest()->headers->get('x-dm-user');
            $password = $event->getRequest()->headers->get('x-dm-pass');
            if (isset($username, $password)) {
                $this->logger->info("Authenticating $username...");
                $existingUser = $this->dmEm->getRepository(Users::class)->findOneBy([
                    'username' => $username,
                    'password' => $password
                ]);

                if (is_null($existingUser)) {
                    throw new UnauthorizedHttpException('Invalid credentials!');
                }

                if ($controller[0] instanceof RequiresAdminEdgeCreatorController) {
                    if (null === $this->dmEm->getRepository(UsersPermissions::class)->findOneBy([
                        'username' => $username,
                        'role' => 'EdgeCreator',
                        'privilege' => 'Admin'
                    ])) {
                        throw new HttpException(403, 'You need admin rights!');
                    }
                }

                $request->getSession()->set('user', ['username' => $existingUser->getUsername(), 'id' => $existingUser->getId()]);
                $this->logger->info("$username is logged in");
            }
            else {
                throw new UnauthorizedHttpException('Credentials are required!');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onKernelRequest',
        ];
    }
}
