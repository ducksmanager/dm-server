<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Wtd\Wtd;

require_once __DIR__.'/vendor/autoload.php';

if (!isset($conf)) {
    $conf = parse_ini_file(__DIR__.'/app/config/config.ini', true);
}

$app = new \Silex\Application();

$app->mount('/', new Wtd());

$app->match('/', function () {
    return '';
});

$app->before(function (Request $request) {
    if (strpos($request->getRequestUri(), '/internal') === 0) {
        return new Response('Unauthorized', 403);
    }
    return true;
});

$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$username = $conf['db']['username'];
$password = $conf['db']['password'];

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'dbname' => 'db301759616',
        'user' => $username,
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
        'server_version' => '15.1',
        'driverOptions' => [
            1002 => 'SET NAMES utf8'
        ]
    ]
]);

$app->register(new Silex\Provider\SessionServiceProvider());

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

$passwordEncoder = new \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder(5);

$app['security.default_encoder'] = function () use ($passwordEncoder) {
    return $passwordEncoder;
};

$users = array();
array_walk($conf['user_roles'], function($role, $user) use ($passwordEncoder, &$users) {
    list($roleName, $rolePassword) = explode(':', $role);
    $users[$user] = array($roleName, $passwordEncoder->encodePassword($rolePassword, ''));
});

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'collection' => array(
            'pattern' => '^/collection/',
            'http' => true,
            'users' => $users
        )
    )
));

$app->run();
