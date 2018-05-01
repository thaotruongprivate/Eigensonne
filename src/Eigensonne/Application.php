<?php
/**
 * Created by PhpStorm.
 * User: thao.truong
 * Date: 01.05.18
 * Time: 13:55
 */

namespace Eigensonne;

use Eigensonne\Controller\HackerNewsController;
use GuzzleHttp\Client;
use Silex\Provider\TwigServiceProvider;

class Application extends \Silex\Application {

    public function __construct(array $values = []) {
        parent::__construct($values);
        $this['root_dir'] = __DIR__ . '/../..';
        $this->register(new TwigServiceProvider(), [
            'twig.path' => $this['root_dir'] . '/views',
        ]);
    }

    public function mountAll() {
        $this->mount('/', new HackerNewsController($this, new Client()));
    }
}