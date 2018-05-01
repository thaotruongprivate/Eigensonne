<?php
/**
 * Created by PhpStorm.
 * User: thao.truong
 * Date: 01.05.18
 * Time: 22:05
 */

namespace Eigensonne\Tests\Controller;

use Silex\WebTestCase;

class HackerNewsControllerTest extends WebTestCase {

    public function createApplication() {
        $app_env = 'test';
        return require __DIR__ . '/../../../../web/index.php';
    }

    public function testNewsPageDisplaysHackerNewsOnFirstPage() {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/news');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('Top hacker news', $crawler->filter('h1')->text());
        $this->assertEquals('Hacker news', $crawler->filter('title')->text());
        $this->assertEquals(10, $crawler->filter('table.hackerNews tr')->count());
        $this->assertEquals(0, $crawler->filter('li.previous')->count());
        $this->assertEquals(1, $crawler->filter('li.next')->count());
        $this->assertEquals('/news?p=2', $crawler->filter('li.next a')->attr('href'));
    }

    public function testNewsPageDisplaysHackerNewsOnThirdPage() {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/news?p=3');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('Top hacker news', $crawler->filter('h1')->text());
        $this->assertEquals('Hacker news', $crawler->filter('title')->text());
        $this->assertEquals(10, $crawler->filter('table.hackerNews tr')->count());
        $this->assertEquals('/news?p=2', $crawler->filter('li.previous a')->attr('href'));
        $this->assertEquals('/news?p=4', $crawler->filter('li.next a')->attr('href'));
    }

    public function testThatOtherFiltersAreKeptInPreviousAndNextLinks() {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/news?v=tasty&p=5');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('/news?v=tasty&p=4', $crawler->filter('li.previous a')->attr('href'));
        $this->assertEquals('/news?v=tasty&p=6', $crawler->filter('li.next a')->attr('href'));
    }

}
