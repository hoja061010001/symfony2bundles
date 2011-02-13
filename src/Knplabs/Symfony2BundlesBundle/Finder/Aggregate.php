<?php

namespace Knplabs\Symfony2BundlesBundle\Finder;

class Aggregate implements FinderInterface
{
    protected $finders;

    public function __construct(array $finders = array())
    {
        $this->setFinders($finders);
    }

    /**
     * {@inheritDoc}
     */
    public function find()
    {
        $repos = array();
        foreach ($this->finders as $finder) {
            $repos+= $finder->find();
        }

        return $repos;
    }

    public function addFinder(FinderInterface $finder)
    {
        $this->finders[] = $finder;
    }

    public function getFinders()
    {
        return $this->finders();
    }

    public function setFinders(array $finders)
    {
        $this->finders = array();
        foreach ($finders as $finder) {
            $this->addFinder($finder);
        }
    }
}
