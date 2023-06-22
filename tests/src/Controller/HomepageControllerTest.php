<?php

namespace App\Tests\src\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class HomepageControllerTest extends WebTestCase
{
    /**
     * Got a request on homepage("/")
     * @return Crawler
     */
    private function requestHomepage() : Crawler
    {
        $client = static::createClient();
        return $client->request('GET', '/');
    }

    /**
     * See if "/" got code 200 response
     * @return void
     */
    public function testHomePage(){
        $this->requestHomepage();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * See if homepage's title is ok for SEO
     * @return void
     */
    public function testTitleHomePage(){
        $this->requestHomepage();
        self::assertSelectorTextContains('h1', 'Bienvenue sur CESI BLOG');
    }
}