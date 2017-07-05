<?php
namespace DmServer\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractInternalController extends AbstractController
{
    /**
     * @param Application $app
     * @param callable $function
     * @return mixed|Response
     * @throws \Exception
     */
    protected abstract static function wrapInternalService($app, $function);
}
