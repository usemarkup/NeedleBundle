<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Terms\TermsServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddTermsPass implements CompilerPassInterface
{
    use AddServiceLocatorArgumentTrait;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $locatorId = TermsServiceLocator::class;

        $locator = $container->getDefinition($locatorId);
        foreach ($container->findTaggedServiceIds('markup_needle.terms') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    continue;
                }
                $this->registerServiceToLocator($attributes['alias'], $id, $locator);
            }
        }
    }
}
