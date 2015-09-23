<?php

namespace Seretalabs\MonologFluentdBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('monolog_fluentd');

        $rootNode->children()
			        ->scalarNode('port')->defaultValue(24224)->end()
			        ->scalarNode('host')->defaultValue('localhost')->end()
			        ->scalarNode('level')->defaultValue(constant('Monolog\Logger::DEBUG'))->end()
	                ->booleanNode('bubble')->defaultValue(true)->end()
	                ->scalarNode('env')->defaultValue('none')->end()
	                ->scalarNode('tag')->defaultValue('backend')->end()
            ->end();

        return $treeBuilder;
    }
}
