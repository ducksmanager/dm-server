<?php
use DmServer\Controllers\AbstractController;
use DmServer\SpoolStub;
use Silex\Application;
use Silex\Provider\LocaleServiceProvider;
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

$app->before(function (Request $request) {
    if (strpos($request->getRequestUri(), '/internal') === 0) {
        return new Response('Unauthorized', Response::HTTP_FORBIDDEN);
    }
    return true;
}, Application::EARLY_EVENT);

$app->register(new Sorien\Provider\PimpleDumpProvider(), [
    'pimpledump.output_dir' => __DIR__]
);

$app->register(new Silex\Provider\LocaleServiceProvider());

if ($forTest) {
    $app->register(new Silex\Provider\MonologServiceProvider(), [
        'monolog.level' => \Monolog\Logger::INFO,
    ]);
}
else {
    $app->register(new Silex\Provider\MonologServiceProvider(), [
        'monolog.logfile' => 'php://stdout',
        'monolog.level' => \Monolog\Logger::INFO,
    ]);
}

$app->register(new LocaleServiceProvider());
$app['locale'] = 'fr';

$app->register(new Silex\Provider\TranslationServiceProvider(), [
    'locale_fallbacks' => ['fr'],
]);

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SwiftmailerServiceProvider());

if ($forTest) {
    $app['swiftmailer.spool'] = function () {
        return new SpoolStub();
    };
}
else {
    $app['swiftmailer.transport'] = (new Swift_SmtpTransport(DmServer::$settings['smtp_host'], 25, 'tls'))
        ->setUsername(DmServer::$settings['smtp_username'])
        ->setPassword(DmServer::$settings['smtp_password']);
}

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

        foreach(['fr'] as $l10n) {
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

$users = [];
array_walk($roles, function($role, $user) use ($passwordEncoder, &$users) {
    list($roleName, $rolePassword) = explode(':', $role);
    $users[$user] = [$roleName, $passwordEncoder->encodePassword($rolePassword, '')];
});

$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'rawsql' => [
            'pattern' => '^/rawsql',
            'http' => true,
            'users' => ['rawsql' => $users['rawsql']]
        ],
        'admin' => [
            'pattern' => '^/ducksmanager/resetDemo',
            'http' => true,
            'users' => ['admin' => $users['admin']]
        ],
        'collection' => [
            'pattern' => '^/collection/',
            'http' => true,
            'users' => [
                'ducksmanager' => $users['ducksmanager'],
                'whattheduck' => $users['whattheduck']
            ]
        ],
        'edgecreator' => [
            'pattern' => '^/edgecreator/',
            'http' => true,
            'users' => [
                'edgecreator' => $users['edgecreator']
            ]
        ]
    ]
]);

$app->run();
