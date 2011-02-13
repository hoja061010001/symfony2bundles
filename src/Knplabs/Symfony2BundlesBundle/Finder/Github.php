<?php

namespace Knplabs\Symfony2BundlesBundle\Finder;

use phpGithubApi;

/**
 * Finds github Symfony2 repositories using the github search
 *
 * @package Symfony2BundlesBundle
 */
class Github implements FinderInterface
{
    /**
     * Constructor
     *
     * @param  phpGithubApi $github  The github api wrapper
     * @param  query        $query   The query
     * @parma  integer      $pageMax The max number of pages to search on
     */
    public function __construct(phpGithubApi $github, $query, $pageMax)
    {
        $this->github = $github;
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
            $repos+= $this->github->getRepoApi()->search($this->query, 'php', $page);
        } while ($page < $this->pageMax);

        return $repos;
    }
}
