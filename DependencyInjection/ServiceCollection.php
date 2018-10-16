<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection;

use Psr\Container\ContainerInterface;

/**
 * Auxiliary class to allow having collections of services that can be lazily fetched/ iterated through.
 */
class ServiceCollection implements \IteratorAggregate
{
    /**
     * @var ContainerInterface
     */
    private $locator;

    /**
     * @var string[]
     */
    private $serviceKeys;

    public function __construct(ContainerInterface $locator, array $serviceKeys)
    {
        $this->locator = $locator;
        $this->serviceKeys = $serviceKeys;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_map(
            function ($key) {
                return $this->locator->get($key);
            },
            $this->serviceKeys
        ));
    }
}
