<?php
namespace Wtd;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Silex\Application\TranslationTrait;

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
    
    protected static function callInternal(Application $app, $url, $type, $parameters)
    {
        if ($type === 'GET') {
            $subRequest = Request::create('/internal' . $url . '/' . implode('/', array_values($parameters)));
        }
        else {
            $subRequest = Request::create('/internal' . $url, 'POST', $parameters);
        }
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }

    /**
     * @param Application $app
     * @param string $string
     * @return string
     */
    protected static function translate(Application $app, $string) {
        /** @var TranslationTrait $translator */
        $translator = $app['translator'];

        return $translator->trans($string);
    }
}
