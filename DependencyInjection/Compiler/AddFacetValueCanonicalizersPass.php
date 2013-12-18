<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

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
        if (!$container->hasDefinition($canonicalizerId)) {
            return;
        }

        $canonicalizer = $container->getDefinition($canonicalizerId);
        foreach ($container->findTaggedServiceIds('markup_needle.facet_value_canonicalizer') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['facet'])) {
                    continue;
                }
                $canonicalizer->addMethodCall('addCanonicalizerForFacetName', array(new Reference($id), $attributes['facet']));
            }
        }
    }
}
