<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Service\SearchServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterSearchServicesPass implements CompilerPassInterface
{
    use AddServiceLocatorArgumentTrait;

    public function process(ContainerBuilder $container)
    {
        $locator = $container->getDefinition(SearchServiceLocator::class);
        foreach ($container->findTaggedServiceIds('markup_needle.search_service') as $id => $tags) {
            foreach ($tags as $attrs) {
                if (!isset($attrs['alias'])) {
                    continue;
                }
                $this->registerServiceToLocator($attrs['alias'], $id, $locator);
            }
        }
    }
}
