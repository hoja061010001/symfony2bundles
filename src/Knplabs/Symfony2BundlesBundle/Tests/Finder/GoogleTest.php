<?php

namespace Knplabs\Symfony2BundlesBundle\Finder;

use Symfony\Component\DomCrawler\Crawler;
use PHPUnit_Framework_TestCase;

class GoogleTest extends PHPUnit_Framework_TestCase
{
    public function testFind()
    {
        $crawler1 = new Crawler(null, 'http://www.google.com/seach?q=foo');
        $crawler1->addContent(file_get_contents(__DIR__ . '/../fixtures/google1.html'), 'text/html');

        $crawler2 = new Crawler(null, 'http://www.google.com/seach?q=foo');
        $crawler2->addContent(file_get_contents(__DIR__ . '/../fixtures/google2.html'), 'text/html');

        $client = $this->getClientMock();
        $client->expects($this->at(0))
            ->method('request')
            ->will($this->returnValue($crawler1));
        $client->expects($this->at(1))
            ->method('request')
            ->will($this->returnValue($crawler2));

        $finder = new Google($client, 'foo', 2);

        $repos = $finder->find();

        $this->assertEquals(
            3,
            count($repos),
            '->find() found 3 different repositories'
        );

        $this->assertContains(
            'foo/bar1',
            $repos,
            '->find() found the foo/bar1 repository'
        );

        $this->assertContains(
            'foo/bar2',
            $repos,
            '->find() found the foo/bar2 repository'
        );

        $this->assertContains(
            'foo/bar3',
            $repos,
            '->find() found the foo/bar3 repository'
        );
    }

    public function testProcessPage()
    {
        $crawler = new Crawler(null, 'http://www.google.com/seach?q=foo');
        $crawler->addContent(file_get_contents(__DIR__ . '/../fixtures/google1.html'), 'text/html');

        $client = $this->getClientMock();
        $client->expects($this->once())
            ->method('request')
            ->will($this->returnValue($crawler));

        $finder = new Google($client, 'foo', 1);
        $repos = $finder->processPage(1);

        $this->assertEquals(
            2,
            count($repos),
            '->findPage() found the two repositories'
        );

        $this->assertContains(
            'foo/bar1',
            $repos,
            '->findPage() found the foo/bar1 repository'
        );

        $this->assertContains(
            'foo/bar2',
            $repos,
            '->findPage() found the foo/bar2 repository'
        );
    }

    public function testExtractRepos()
    {
        $finder = new Google($this->getClientMock(), 'foo', 1);

        $crawler = new Crawler(null, 'http://www.google.com/seach?q=foo');
        $crawler->addContent(file_get_contents(__DIR__ . '/../fixtures/google1.html'), 'text/html');

        $links = $finder->extractRepos($crawler);

        $this->assertEquals(
            2,
            count($links),
            '->extractRepos() found the two repository links'
        );

        $this->assertContains(
            'foo/bar1',
            $links,
            '->extractRepos() the foo/bar1 repository link was found'
        );

        $this->assertContains(
            'foo/bar2',
            $links,
            '->extractRepos() the foo/bar2 repository link was found'
        );
    }

    public function testExtractRepoFromUrl()
    {
        $finder = new Google($this->getClientMock(), 'foo', 1);

        $this->assertEquals(
            'username/name',
            $finder->extractRepoFromUrl('http://github.com/username/name'),
            '->extractRepoFromUrl() returns the "username/name" part of a valid uri'
        );
    -
        $this->assertEquals(
            'username/name',
            $finder->extractRepoFromUrl('https://github.com/username/name'),
            '->extractRepoFromUrl() works with secure http urls'
        );
        $this->assertEquals(
            false,
            $finder->extractRepoFromUrl(''),
            '->extractRepoFromUrl() returns false when the given url is empty'
        );

        $this->assertEquals(
            false,
            $finder->extractRepoFromUrl('http://www.monsite.com/hello/test'),
            '->extractRepoFromUrl() returns false when the url is invalid'
        );
    }

    public function testGetQueryUrl()
    {
        $finder = new Google($this->getClientMock(), 'foo', 1);

        $this->assertEquals(
            'http://www.google.com/search?q=foo+bar',
            $finder->getQueryUrl('foo bar'),
            '->getQueryUrl() for a simple query'
        );

        $this->assertEquals(
            'http://www.google.com/search?q=foo+bar&start=2',
            $finder->getQueryUrl('foo bar', 2),
            '->getQueryUrl() append the page number if needed'
        );
    }

    protected function getClientMock()
    {
        return $this->getMock('Goutte\Client');
    }
}
