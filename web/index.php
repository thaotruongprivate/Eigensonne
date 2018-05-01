<?php

use Eigensonne\Controller\HackerNewsController;
use Eigensonne\Application;
use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application([
    'debug' => true
]);

if (isset($app_env) && in_array($app_env, array('prod','dev','test')))
    $app['env'] = $app_env;
else
    $app['env'] = 'prod';

$app->mount('/', new HackerNewsController($app, new Client()));

if ('test' === $app['env']) {
    return $app;
} else {
    $app->run();
}