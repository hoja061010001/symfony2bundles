<?php

namespace Knplabs\Symfony2BundlesBundle\Finder;

/**
 * Interface that must be implemented be the repo finders
 *
 * @package Symfony2BundlesBundle
 */
interface FinderInterface
{
    /**
     * Finds repositories and returns an array of results
     *
     * @return array
     */
    function find();
}
