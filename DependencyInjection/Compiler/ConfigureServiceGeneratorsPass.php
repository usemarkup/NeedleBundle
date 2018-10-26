<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Container\ServiceClosure;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Configure any services whose job is to provide a new unshared instance of another service on invocation.
 */
class ConfigureServiceGeneratorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('markup_needle.service_generator') as $id => $tags) {
            $generator = $container->findDefinition($id);
            $targetServiceId = $generator->getArgument(0);
            $locator = (new Definition(ServiceLocator::class))
                ->setArguments([[ServiceClosure::ALIAS => new Reference($targetServiceId)]])
                ->setPublic(false)
                ->addTag('container.service_locator');
            $locatorId = $id.'._generator';
            $container->setDefinition($locatorId, $locator);
            $generator->setArgument('$container', new Reference($locatorId));
        }
    }
}
