<?php
/**
 * Created by PhpStorm.
 * User: thao.truong
 * Date: 01.05.18
 * Time: 01:49
 */

namespace Eigensonne\Controller;

use Eigensonne\Application;
use GuzzleHttp\ClientInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Silex\Api\ControllerProviderInterface;
use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

class HackerNewsController implements ControllerProviderInterface {

    const NEWS_PER_PAGE = 10;

    private $hackerNewsApiEndpoint = 'https://hacker-news.firebaseio.com/v0/';

    /**
     * @var Client
     */
    private $guzzle;
    private $container;

    public function __construct(Application $app, ClientInterface $guzzle) {
        $this->guzzle = $guzzle;
        $this->container = $app;
    }

    public function connect(SilexApplication $app) {
        $controllers = $this->container['controllers_factory'];
        $controllers->get('/', [$this, 'actionHomepage'])->bind('homepage');
        $controllers->get('news', [$this, 'actionIndex'])->bind('news');
        return $controllers;
    }

    public function actionHomepage() {
        return new Response($this->render('welcome.html.twig'));
    }

    public function actionIndex(Request $request) {

        $page = $request->get('p') ?? 1;
        $requestParameters = $request->query->all();
        unset($requestParameters['p']);

        $newsList = $this->getAllNews($page);
        return new Response(
            $this->render('hackerNews/index.html.twig', [
                'newsList' => $newsList,
                'title' => 'Hacker news',
                'headText' => 'Top hacker news',
                'nextUrl' => $this->getUrlGenerator()->generate('news',
                    array_merge($requestParameters,
                        ['p' => $page + 1]
                    )
                ),
                'previousUrl' => $this->getUrlGenerator()->generate('news',
                    array_merge($requestParameters,
                        $page > 2 ? ['p' => $page - 1] : []
                    )
                ),
                'currentPage' => $page
            ])
        );

    }

    /**
     * @param int $page
     * @return array|null
     */
    private function getAllNews($page = 1): ?array {
        $response = $this->guzzle->get($this->hackerNewsApiEndpoint . 'topstories.json');
        if ($response->getStatusCode() === 200) {
            $newsList = \json_decode($response->getBody(), true);
            $data = [];
            $index = ($page - 1) * self::NEWS_PER_PAGE;
            while ($index < self::NEWS_PER_PAGE * $page) {
                $data[] = $this->getNewsDetails($newsList[$index]);
                $index++;
            }
            return $data;
        }
        return null;
    }

    private function getNewsDetails($id): ?array {
        $response = $this->guzzle->get(sprintf($this->hackerNewsApiEndpoint . 'item/%d.json', $id));
        if ($response->getStatusCode() === 200) {
            $details = \json_decode($response->getBody(), true);
            if (isset($details['url'])) {
                $host = parse_url($details['url'], PHP_URL_HOST);
                $host = array_reverse(explode('.', $host));
                $details['host'] = $host[1] . '.' . $host[0];
            } else {
                $details['url'] = 'https://news.ycombinator.com/item?id=' . $details['id'];
            }
            $details['time'] = $this->getTimeRelative($details['time']);
            return $details;
        }

        return null;
    }

    protected function getTimeRelative(int $timestamp): string {

        $diff = time() - $timestamp;

        $minutes = floor($diff / 60);
        if ($minutes < 60) {
            return sprintf('%d minute' . ($minutes > 1 ? 's' : '') . ' ago', $minutes);
        }

        $hours = floor($minutes / 60);
        if ($hours < 24) {
            return sprintf('%d hour' . ($hours > 1 ? 's' : '') . ' ago', $hours);
        }

        $days = floor($hours / 24);
        if ($days < 30) {
            return sprintf('%d day' . ($days > 1 ? 's' : '') . ' ago', $days);
        }

        $months = floor($days / 30);
        if ($months < 12) {
            return sprintf('%d month' . ($months > 1 ? 's' : '') . ' ago', $months);
        }

        $years = floor($days / 365);
        return sprintf('%d year' . ($years > 1 ? 's' : '') . ' ago', $years);
    }

    protected function getUrlGenerator(): UrlGenerator {
        return $this->container['url_generator'];
    }

    protected function render(string $view, array $values = []): string {
        return $this->container['twig']->render($view, $values);
    }
}