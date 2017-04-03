<?php

namespace Loevgaard\DataAwareCommandBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('loevgaard_data_aware_command');

        $rootNode
            ->children()
                ->scalarNode('data_dir')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
