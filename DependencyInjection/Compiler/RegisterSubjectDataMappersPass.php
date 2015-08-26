<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
* Registers subject mappers for corpora that are tagged elsewhere in the application.
*/
class RegisterSubjectDataMappersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $mapperProviderId = 'markup_needle.exporter.subject_data_mapper_provider';
        if (!$container->has($mapperProviderId)) {
            return;
        }

        $mapperProvider = $container->getDefinition($mapperProviderId);
        foreach ($container->findTaggedServiceIds('markup_needle.subject_mapper') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['corpus'])) {
                    continue;
                }
                $mapperProvider->addMethodCall('addMapper', [$attributes['corpus'], new Reference($id)]);
            }
        }
    }
}
