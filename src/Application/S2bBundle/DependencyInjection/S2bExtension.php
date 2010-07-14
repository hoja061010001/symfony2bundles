<?php

namespace Application\S2bBundle\DependencyInjection;

use Symfony\Components\DependencyInjection\Loader\LoaderExtension;
use Symfony\Components\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Components\DependencyInjection\BuilderConfiguration;

class S2bExtension extends LoaderExtension
{
    public function configLoad($config, BuilderConfiguration $configuration)
    {
        $loader = new XmlFileLoader(__DIR__.'/../Resources/config');
        $configuration->merge($loader->load('config.xml'));
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    public function getAlias()
    {
        return 's2b';
    }

}
