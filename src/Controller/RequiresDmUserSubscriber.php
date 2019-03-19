<?php

namespace App\Controller;

use App\Entity\Dm\Users;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

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

    public function __construct(EntityManagerInterface $dmEm, LoggerInterface $logger) {
        $this->dmEm = $dmEm;
        $this->logger = $logger;
    }

    public function onKernelController(FilterControllerEvent $event): void
    {
        $request = $event->getRequest();
        $controller = $event->getController();

        if (is_array($controller) && $controller[0] instanceof RequiresDmUserController) {
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
            KernelEvents::CONTROLLER => ['onKernelController', 2],
        ];
    }
}
