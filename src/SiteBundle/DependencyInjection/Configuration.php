<?php

// src/Acme/SocialBundle/DependencyInjection/Configuration.php
namespace SiteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('site');

        $rootNode->children()->scalarNode('directory')->end();

        return $treeBuilder;
    }
}