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

$app->extend(
    /**
     * @param Translator $translator
     * @param Application $app
     * @return mixed
     */
    'translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/app/locales/en.yml', 'en');
    $translator->addResource('yaml', __DIR__.'/app/locales/fr.yml', 'fr');

    return $translator;
});

$app->run();
