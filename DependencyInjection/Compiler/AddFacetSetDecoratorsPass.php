<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddFacetSetDecoratorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     **/
    public function process(ContainerBuilder $container): void
    {
        $decoratorProvider = $container->findDefinition('markup_needle.facet_set_decorator_provider');

        foreach ($container->findTaggedServiceIds('markup_needle.facetset_decorator') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['facet'])) {
                    continue;
                }

                $decoratorProvider->addMethodCall('addDecorator', [$attributes['facet'], new Reference($id)]);
            }
        }
    }
}
