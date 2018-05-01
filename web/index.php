<?php

use Eigensonne\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application([
    'debug' => true
]);

if (isset($app_env) && in_array($app_env, array('prod','dev','test')))
    $app['env'] = $app_env;
else
    $app['env'] = 'prod';

$app->mountAll();

if ('test' === $app['env']) {
    return $app;
} else {
    $app->run();
}