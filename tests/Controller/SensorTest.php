<?php

namespace App\Tests\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class SensorTest extends GrowbookApplicationTestCase
{

    /**
     *
     * @throws \Exception
     */
    public function testTestDummy(): void
    {

        $this->assertSame(0, 0);
    }

    /**
     *
     * @param null|string $name
     * @param array $data
     * @param string $dataName
     * @throws \Exception
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->setUp();
        self::$entityManager->clear();
    }
//
//    /**
//     * @param Crawler $crawler
//     */
//    protected function checkIndex(Crawler $crawler): void
//    {
//        $this->assertSame(Response::HTTP_OK, self::getClient()->getResponse()->getStatusCode(), $this->getErrorMessage($crawler));
//        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Events")')->count());
//    }
//
//
//
//    /**
//     *
//     * @throws \Exception
//     */
//    public function testTestIndex(): void
//    {
//        $crawler = self::getClient()->request('GET', '/event/');
//        $this->checkIndex($crawler);
//    }
}
