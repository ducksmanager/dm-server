<?php
use DmServer\Controllers\AbstractController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use DmServer\DmServer;

require_once __DIR__.'/vendor/autoload.php';

$forTest = isset($conf);

if ($forTest) {
    DmServer::initSettings('settings.test.ini');
}
else {
    $conf = DmServer::getAppConfig();
    DmServer::initSettings('settings.ini');
}

$app = new \Silex\Application();

$app->mount('/', new DmServer());

$app->match('/', function () {
    return '';
});

$app->match("{url}", function () {
    $response = new Response('', 200);
    $response->headers->set("Access-Control-Allow-Headers", "x-dm-version");
    return $response;
})->assert('url', '.*')->method("OPTIONS");

$app->before(function (Request $request) {
    if (strpos($request->getRequestUri(), '/internal') === 0) {
        return new Response('Unauthorized', Response::HTTP_FORBIDDEN);
    }
    return true;
});

$app->register(new Sorien\Provider\PimpleDumpProvider(), array(
    'pimpledump.output_dir' => __DIR__)
);

$app->register(new Silex\Provider\LocaleServiceProvider());

if ($forTest) {
    $app->register(new Silex\Provider\MonologServiceProvider(), array(
        'monolog.level' => \Monolog\Logger::INFO,
    ));
}
else {
    $app->register(new Silex\Provider\MonologServiceProvider(), array(
        'monolog.logfile' => __DIR__ . '/development.log',
        'monolog.level' => \Monolog\Logger::INFO,
    ));
}

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app['swiftmailer.transport'] = new Swift_NullTransport();
$app['swiftmailer.options'] = array(
    'host' => DmServer::$settings['smtp_host'],
    'port' => '25',
    'username' => DmServer::$settings['smtp_username'],
    'password' => DmServer::$settings['smtp_password'],
    'encryption' => null,
    'auth_mode' => null
);

$app->error(function (\Exception $e, Request $request, $code) {
    return new Response($e->getMessage()."\n\n".$e->getTraceAsString(), $code);
});

if ($forTest) {
    $app['session.test'] = true;
}


$app->extend(
    /**
     * @param Translator $translator
     * @param Application $app
     * @return Translator
     */
    'translator', function(Translator $translator) {
        $translator->addLoader('yaml', new YamlFileLoader());

        foreach(['en', 'fr'] as $l10n) {
            $translator->addResource('yaml', __DIR__.'/app/locales/'.$l10n.'.yml', $l10n);
        }

        return $translator;
    }
);
AbstractController::initTranslation($app);

$passwordEncoder = new \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder(5);

$app['security.default_encoder'] = function () use ($passwordEncoder) {
    return $passwordEncoder;
};

$roles = DmServer::getAppRoles();

$users = array();
array_walk($roles, function($role, $user) use ($passwordEncoder, &$users) {
    list($roleName, $rolePassword) = explode(':', $role);
    $users[$user] = array($roleName, $passwordEncoder->encodePassword($rolePassword, ''));
});

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'rawsql' => array(
            'pattern' => '^/rawsql',
            'http' => true,
            'users' => ['rawsql' => $users['rawsql']]
        ),
        'admin' => array(
            'pattern' => '^/ducksmanager/resetDemo',
            'http' => true,
            'users' => ['admin' => $users['admin']]
        ),
        'collection' => array(
            'pattern' => '^/collection/',
            'http' => true,
            'users' => [
                'ducksmanager' => $users['ducksmanager'],
                'whattheduck' => $users['whattheduck']
            ]
        ),
        'edgecreator' => array(
            'pattern' => '^/edgecreator/',
            'http' => true,
            'users' => [
                'edgecreator' => $users['edgecreator']
            ]
        )
    )
));

$app->run();
