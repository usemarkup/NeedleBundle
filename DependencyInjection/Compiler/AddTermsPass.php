<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddTermsPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $providerId = 'markup_needle.terms_provider';
        if (!$container->hasDefinition($providerId)) {
            return;
        }

        $provider = $container->getDefinition($providerId);
        foreach ($container->findTaggedServiceIds('markup_needle.terms') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    continue;
                }
                $provider->addMethodCall('addService', [$id, $attributes['alias']]);
            }
        }
    }
}
