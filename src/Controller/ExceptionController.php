<?php

namespace App\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Environment;

class ExceptionController extends BaseExceptionController
{
    public function __construct(Environment $twig, bool $debug = false)
    {
        parent::__construct($twig, $debug);
    }

    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null) : Response
    {
        if ($request->attributes->get('showException', $this->debug)) {
            return parent::showAction($request, $exception, $logger);
        }

        return new Response($exception->getMessage(), $exception->getStatusCode());
    }


}