<?php

namespace Wtd;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;

class Wtd extends AppController implements ControllerProviderInterface
{
    public function setup(Application $app)
    {
        $app['debug'] = true;
    }

    /**
     * Connect the controller classes to the routes
     * @param Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        // set up the service container
        $this->setup($app);

        // Load routes from the controller classes
        /** @var ControllerCollection $routing */
        $routing = $app['controllers_factory'];

        $routing->before(function($request, $app) {
            if (preg_match('#^/collection/((?!new/).)+$#', $request->getPathInfo())) {
                $username = $request->request->get('username');
                $password = $request->request->get('password');

                $userCheck = self::callInternal($app, '/user/check', 'GET', [
                    'username' => $username,
                    'password' => $password
                ]);
                if ($userCheck->getStatusCode() !== Response::HTTP_OK) {
                    return $userCheck;
                }
                else {
                    $this->setSessionUser($app, $username, $userCheck->getContent());
                }
            }
        });

        CollectionController::addRoutes($routing);
        InternalController::addRoutes($routing);

        return $routing;
    }
}
