<?php

namespace Application\S2bBundle;

use Application\S2bBundle\DependencyInjection\S2bExtension;
use Symfony\Framework\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Components\DependencyInjection\Loader\Loader;

class S2bBundle extends BaseBundle
{
    public function buildContainer(ParameterBagInterface $parameterBag)
    {
        Loader::registerExtension(new S2bExtension());
    }
}
