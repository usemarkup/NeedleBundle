<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
* Registers facet value canonicalizers against the central canonicalizer service.
*/
class AddFacetValueCanonicalizersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     **/
    public function process(ContainerBuilder $container)
    {
        $canonicalizerId = 'markup_needle.facet.value_canonicalizer';
        if (!$container->has($canonicalizerId)) {
            return;
        }

        $canonicalizer = $container->findDefinition($canonicalizerId);
        foreach ($container->findTaggedServiceIds('markup_needle.facet_value_canonicalizer') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['facet'])) {
                    continue;
                }
                $canonicalizer->addMethodCall('addCanonicalizerForFacetName', [new Reference($id), $attributes['facet']]);
            }
        }
    }
}
