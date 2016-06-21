<?php

namespace Wtd;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CollectionController extends AppController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post('/collection/new', function (Application $app) {

            $subRequest = Request::create('/internal/test', 'POST');
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
        });
    }
}
