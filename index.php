<?php
use DmServer\AppController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use DmServer\DmServer;

require_once __DIR__.'/vendor/autoload.php';

if (!isset($conf)) {
    $conf = DmServer::getAppConfig('config.db.ini');
    $settings = DmServer::initSettings('settings.ini');
}

$app = new \Silex\Application();

$app->mount('/', new DmServer());

$app->match('/', function () {
    return '';
});

$app->before(function (Request $request) {
    if (strpos($request->getRequestUri(), '/internal') === 0) {
        return new Response('Unauthorized', 403);
    }
    return true;
});

$app->register(new Sorien\Provider\PimpleDumpProvider(), array(
    'pimpledump.output_dir' => __DIR__)
);

$app->register(new Silex\Provider\LocaleServiceProvider());

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/development.log',
    'monolog.level' => \Monolog\Logger::INFO,
));

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => DmServer::getConnectionParams($conf['db'])
]);

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => DmServer::getConnectionParams($conf['db_coa'])
]);

@unlink($conf['db']['path']);

$app->register(new Silex\Provider\SessionServiceProvider());
$app['session.test'] = true;


$app->extend(
    /**
     * @param Translator $translator
     * @param Application $app
     * @return Translator
     */
    'translator', function(Translator $translator) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/app/locales/en.yml', 'en');
    $translator->addResource('yaml', __DIR__.'/app/locales/fr.yml', 'fr');

    return $translator;
});
AppController::initTranslation($app);

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
        'all' => array(
            'pattern' => '^/rawsql',
            'http' => true,
            'users' => ['rawsql' => $users['rawsql']]
        ),
        'collection' => array(
            'pattern' => '^/collection/',
            'http' => true,
            'users' => [
                'ducksmanager' => $users['ducksmanager'],
                'whattheduck' => $users['whattheduck']
            ]
        )
    )
));

$app->run();
