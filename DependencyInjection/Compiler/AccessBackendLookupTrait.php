<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Corpus\CorpusBackendProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Trait providing a method to be used by compiler passes for accessing the defined corpus => backend map.
 */
trait AccessBackendLookupTrait
{
    private function getBackendLookup(ContainerBuilder $container): array
    {
        return array_map(
            function (string $parameter) use ($container) {
                return $this->resolveParameter($parameter, $container);
            },
            $container->getDefinition(CorpusBackendProvider::class)->getArgument(0)
        );
    }

    private function resolveParameter(string $parameter, ContainerBuilder $container): string
    {
        if (!preg_match('/^%[\w\.]+%$/', $parameter)) {
            return $parameter;
        }

        return $container->getParameter(substr($parameter, 1, strlen($parameter)-2));
    }
}
