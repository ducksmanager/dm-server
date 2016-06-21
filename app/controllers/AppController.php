<?php
namespace Wtd;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

abstract class AppController
{
    /**
     * @param Application $app
     * @return Request
     */
    protected function getRequestContent(Application $app)
    {
        /** @var Request $request */
        $request = $app['request'];

        return $request->getContent();
    }
}
