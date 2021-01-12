<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Facet\AggregateFacetValueCanonicalizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
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
    public function process(ContainerBuilder $container): void
    {
        $aggregateFacetValueCanonicalizer = $container->findDefinition(AggregateFacetValueCanonicalizer::class);

        $servicesKeyedByFacet = [];

        foreach ($container->findTaggedServiceIds('markup_needle.facet_value_canonicalizer') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['facet'])) {
                    continue;
                }

                $servicesKeyedByFacet[$attributes['facet']] = new Reference($id);
            }
        }

        $aggregateFacetValueCanonicalizer->addArgument(ServiceLocatorTagPass::register($container, $servicesKeyedByFacet));
    }
}
