<?php

namespace Knplabs\Symfony2BundlesBundle\Finder;

use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;

/**
 * Finds github Symfony2 repositories using google search
 *
 * @package Symfony2BundlesBundle
 */
class Google implements FinderInterface
{
    protected $client;
    protected $query;
    protected $pageMax;

    /**
     * Constructor
     *
     * @param  Client   $client  The Goutte client used to perform requests
     * @param  string   $query   The query
     * @param  integer  $pageMax The maximum number of pages to search on
     */
    public function __construct(Client $client, $query, $pageMax)
    {
        $this->client = $client;
        $this->query = $query;
        $this->pageMax = $pageMax;
    }

    /**
     * {@inheritDoc}
     */
    public function find()
    {
        $page = 0;

        $repos = array();
        do {
            $page++;
            $repos = array_merge(
                $repos,
                $this->processPage($page)
            );
        } while ($page < $this->pageMax);

        return array_unique($repos);
    }

    /**
     * Process the find query at the specified page number
     *
     * @param  integer $page
     *
     * @return array
     */
    public function processPage($page)
    {
        $crawler = $this->executeQuery($this->query, $page);

        return $this->extractRepos($crawler);
    }

    /**
     * Executes the query
     *
     * @param  string  $query The query to execute
     * @param  integer $page  The page to retrieve (default 1)
     *
     * @return Crawler
     */
    public function executeQuery($query, $page = 1)
    {
        $url = $this->getQueryUrl($query, $page);

        return $this->client->request('GET', $url);
    }

    /**
     * Returns an array of urls for the given crawler
     *
     * @param  Crawler $crawler
     *
     * @return array
     */
    public function extractRepos(Crawler $crawler)
    {
        $links = $crawler->filter('#center_col ol li h3 a');

        if (0 === $links->count()) {
            return array();
        }

        $repos = array();
        foreach($links->extract('href') as $url) {
            $repo = $this->extractRepoFromUrl($url);
            if (false !== $repo) {
                $repos[] = $repo;
            }
        }

        return array_unique($repos);
    }

    /**
     * Returns the repo from the given url
     *
     * @param  string $url
     *
     * @return string The repo, or FALSE on failure
     */
    public function extractRepoFromUrl($url)
    {
        if(preg_match('#^https?://github.com/([\w-]+/[\w-]+).*$#', $url, $match)) {
            return $match[1];
        }

        return false;
    }

    /**
     * Returns the page for the given query and page
     *
     * @return string
     */
    public function getQueryUrl($query, $page = 1)
    {
        $url = 'http://www.google.com/search?q=' . urlencode($query);

        if ($page > 1) {
            $url.= '&start=' . $page;
        }

        return $url;
    }
}
