<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ExceptionListener {
    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            $exception = $event->getRequest()->attributes->get('exception');
            if (!is_null($exception)) {
                $event->setResponse(new Response($exception->getMessage()));
            }
        }
    }
}