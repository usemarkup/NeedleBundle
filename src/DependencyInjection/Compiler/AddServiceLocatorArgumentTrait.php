<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

trait AddServiceLocatorArgumentTrait
{
    private function registerServiceToLocator($key, string $serviceId, Definition $locator)
    {
        $locator->setArguments([
            array_merge(
                $locator->getArguments()[0] ?? [],
                [
                    $key => new Reference($serviceId),
                ]
            ),
        ]);
    }
}
