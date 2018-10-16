<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * A compiler pass for setting up service collections, being a locator that can return a group of services for one alias.
 */
class CreateServiceCollectionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('markup_needle.service_collection') as $id => $tags) {
            $service = $container->findDefinition($id);
            $args = iterator_to_array(
                new \RecursiveIteratorIterator(
                    new \RecursiveArrayIterator($service->getArguments())
                )
            );
            $locator = (new Definition(ServiceLocator::class))
                ->setArguments([
                    array_map(
                        function ($arg) use ($id) {
                            return $this->createClosureArgument($arg, $id);
                        },
                        $args
                    ),
                ])
                ->addTag('container.service_locator')
                ->setPublic(false);
            $locatorId = implode('.', [$id, substr(str_shuffle(md5(microtime())), 0, 5), 'locator']);
            $container->setDefinition($locatorId, $locator);
            $service->setArguments([
                new Reference($locatorId),
                array_keys($args),
            ]);
        }
    }

    private function createClosureArgument($arg, string $id)
    {
        if ($arg instanceof ServiceClosureArgument) {
            return $arg;
        } elseif ($arg instanceof Reference) {
            return new ServiceClosureArgument($arg);
        } elseif (is_string($arg)) {
            return new ServiceClosureArgument(new Reference($arg));
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Invalid argument for a service collection "%s".',
                $id
            ));
        }
    }
}
