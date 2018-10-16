<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Suggest\SuggestServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddSuggestersPass implements CompilerPassInterface
{
    use AddServiceLocatorArgumentTrait;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $locator = $container->getDefinition(SuggestServiceLocator::class);
        foreach ($container->findTaggedServiceIds('markup_needle.suggester') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    continue;
                }
                $this->registerServiceToLocator($attributes['alias'], $id, $locator);
            }
        }
    }
}
