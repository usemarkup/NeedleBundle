<?php

namespace Markup\NeedleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('markup_needle');

        $rootNode
            ->fixXmlConfig('corpus', 'corpora')
            ->children()
                ->arrayNode('backend')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('client')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('debug')
                    ->defaultValue(false)
                ->end()
                ->arrayNode('intercepts')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_domain')
                            ->defaultValue('default')
                        ->end()
                        ->arrayNode('domains')
                        ->end()
                        ->arrayNode('definitions')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->fixXmlConfig('filter')
                                ->children()
                                    ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                                    ->arrayNode('terms')
                                        ->cannotBeEmpty()
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->scalarNode('domain')->cannotBeEmpty()->end()
                                    ->scalarNode('corpus')->cannotBeEmpty()->end()
                                    ->arrayNode('filters')
                                        ->useAttributeAsKey('attr')
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->scalarNode('route')->cannotBeEmpty()->end()
                                    ->arrayNode('route_params')
                                        ->useAttributeAsKey('param')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('corpora')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('schedule_index_on_events')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('log_bad_requests')
                    ->defaultFalse()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
