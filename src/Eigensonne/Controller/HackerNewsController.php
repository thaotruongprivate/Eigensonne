<?php
/**
 * Created by PhpStorm.
 * User: thao.truong
 * Date: 01.05.18
 * Time: 01:49
 */

namespace Eigensonne\Controller;

use Eigensonne\Application;
use Eigensonne\Utilities\Formatter;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Silex\Api\ControllerProviderInterface;
use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

class HackerNewsController implements ControllerProviderInterface {

    const NEWS_PER_PAGE = 10;

    private $hackerNewsApiEndpoint = 'https://hacker-news.firebaseio.com/v0/';
    private $frontendUrl = 'https://news.ycombinator.com';

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

        return new Response(
            $this->render('hackerNews/index.html.twig', [
                'newsList' => $this->getTopNews($page),
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
                'currentPage' => $page,
                'websiteUrl' => $this->frontendUrl
            ])
        );

    }

    private function getTopNews(int $page = 1): ?array {
        $response = $this->guzzle->get($this->hackerNewsApiEndpoint . 'topstories.json');
        if ($response->getStatusCode() === 200) {
            $newsList = \json_decode($response->getBody(), true);
            $data = [];
            $index = ($page - 1) * self::NEWS_PER_PAGE;
            while ($index < self::NEWS_PER_PAGE * $page) {
                $newsDetails = $this->getNewsDetails($newsList[$index]);
                $newsDetails['order'] = $index + 1;
                $data[] = $newsDetails;
                $index++;
            }
            return $data;
        }
        return null;
    }

    private function getNewsDetails(int $id): ?array {
        $response = $this->guzzle->get(sprintf($this->hackerNewsApiEndpoint . 'item/%d.json', $id));
        if ($response->getStatusCode() === 200) {
            $details = \json_decode($response->getBody(), true);
            if (isset($details['url'])) {
                $host = parse_url($details['url'], PHP_URL_HOST);
                $hostArray = array_reverse(explode('.', $host));
                $details['host'] = $hostArray[1] . '.' . $hostArray[0];
            } else {
                $details['url'] = $this->frontendUrl . '/item?id=' . $details['id'];
            }
            $details['time'] = Formatter::timeIntervalRelativeToNow($details['time']);
            return $details;
        }

        return null;
    }

    protected function getUrlGenerator(): UrlGenerator {
        return $this->container['url_generator'];
    }

    protected function render(string $view, array $values = []): string {
        return $this->container['twig']->render($view, $values);
    }
}