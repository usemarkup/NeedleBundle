<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
* Registers corpus-specific search intercept mappers to the search intercept mapper service.
*/
class RegisterSearchInterceptMappersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $searchMapperId = 'markup_needle.intercept_mapper.search';
        if (!$container->has($searchMapperId)) {
            return;
        }

        $searchMapper = $container->getDefinition($searchMapperId);
        foreach ($container->findTaggedServiceIds('markup_needle.search_intercept_mapper') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['corpus'])) {
                    continue;
                }
                $searchMapper->addMethodCall('addSearchInterceptMapper', array($attributes['corpus'], new Reference($id)));
            }
        }
    }
}
