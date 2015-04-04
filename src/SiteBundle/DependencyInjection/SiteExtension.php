<?php

namespace SiteBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SiteExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // The next 2 lines are pretty common to all Extension templates.
        $configuration = new Configuration();
        
        $processedConfig = $this->processConfiguration( $configuration, $configs );
        
        $container->setParameter('directory', $processedConfig['directory']);
    }
}