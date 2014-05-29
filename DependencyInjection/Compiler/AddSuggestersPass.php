<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddSuggestersPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $providerId = 'markup_needle.suggester_provider';
        if (!$container->hasDefinition($providerId)) {
            return;
        }

        $provider = $container->getDefinition($providerId);
        foreach ($container->findTaggedServiceIds('markup_needle.suggester') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    continue;
                }
                $provider->addMethodCall('addService', [$id, $attributes['alias']]);
            }
        }
    }
}
