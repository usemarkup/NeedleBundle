<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddSpecializationContextFiltersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $compositeId = 'markup_needle.specialization_context_group_filter';
        if (!$container->has($compositeId)) {
            return;
        }

        $composite = $container->findDefinition($compositeId);
        foreach (array_keys($container->findTaggedServiceIds('markup_needle.specialization_group_filter')) as $id) {
            $composite->addMethodCall('addFilter', [new Reference($id)]);
        }
    }
}
