<?php

namespace Application\S2bBundle\Sitemap;

use Bundle\SitemapBundle\Sitemap\Provider as ProviderInterface;
use Bundle\SitemapBundle\Sitemap\Sitemap;
use Doctrine\ORM\EntityManager;
use Symfony\Components\Routing\Router;

class Provider implements ProviderInterface
{
    protected $router;
    protected $em;

    function __construct(Router $router, EntityManager $em)
    {
        $this->router = $router;
        $this->router->setContext(array(
            'host' => 'symfony2bundles.org'
        ));
        $this->em = $em;
    }

    public function populate(Sitemap $sitemap)
    {
        foreach($this->getRepository('Repo')->findAll() as $repo) {
            $sitemap->add(
                $this->router->generate('repo_show', array('username' => $repo->getUsername(), 'name' => $repo->getName()), true),
                array(
                    'lastmod' => $repo->getLastCommitAt(),
                    'priority' => 1
                )
            );
        }
    }

    protected function getRepository($class)
    {
        return $this->em->getRepository('Application\S2bBundle\Entities\\'.$class);
    }
}
