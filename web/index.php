<?php

use Eigensonne\Controller\HackerNewsController;
use Eigensonne\Application;
use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application([
    'debug' => true
]);

$app->mount('/', new HackerNewsController($app, new Client()));

$app->run();