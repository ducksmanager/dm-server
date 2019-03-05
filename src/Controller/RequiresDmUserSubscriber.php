<?php

namespace App\Controller;

use App\Entity\Dm\Users;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RequiresDmUserSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
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
                $qb = $this->dmEm->createQueryBuilder();
                $qb
                    ->select('DISTINCT u')
                    ->from(Users::class, 'u')
                    ->andWhere($qb->expr()->eq('u.username', ':username'))
                    ->andWhere($qb->expr()->eq('u.password', ':password'));

                $qb->setParameters([':username' => $username, 'password' => $password]);

                try {
                    /** @var Users $existingUser */
                    $existingUser = $qb->getQuery()->getSingleResult();
                    $request->getSession()->set('user', ['username' => $existingUser->getUsername(), 'id' => $existingUser->getId()]);
                    $this->logger->info("$username is logged in");
                } catch (NoResultException|NonUniqueResultException $e) {
                    throw new UnauthorizedHttpException('Invalid credentials!');
                }
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
