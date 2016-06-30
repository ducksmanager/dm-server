<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Wtd\Wtd;

require_once __DIR__.'/vendor/autoload.php';

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

$conf = parse_ini_file(__DIR__.'/app/config/config.ini');
$username = $conf['username'];
$password = $conf['password'];

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'dbname' => 'db301759616',
        'user' => $username,
        'password' => $password,
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
        'server_version' => '15.1',
        'driverOptions' => [
            1002 => 'SET NAMES utf8'
        ]
    ]
]);

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

$app->run();
