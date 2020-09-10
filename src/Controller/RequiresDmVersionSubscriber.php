<?php

namespace App\Controller;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequiresDmVersionSubscriber implements EventSubscriberInterface
{
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller) && $controller[0] instanceof RequiresDmVersionController) {
            if ($event->getRequest()->headers->has('x-dm-version')) {
                return null;
            }
            throw new HttpException(Response::HTTP_VERSION_NOT_SUPPORTED);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onKernelController',
        ];
    }
}
