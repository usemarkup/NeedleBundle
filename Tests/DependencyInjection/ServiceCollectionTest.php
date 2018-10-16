<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\DependencyInjection;

use Markup\NeedleBundle\DependencyInjection\ServiceCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ServiceCollectionTest extends TestCase
{
    public function testIterate()
    {
        $service1 = new \stdClass();
        $service2 = new \stdClass();
        $locator = new ServiceLocator([
            0 => function () use ($service1) {
                return $service1;
            },
            1 => function () use ($service2) {
                return $service2;
            },
        ]);
        $collection = new ServiceCollection($locator, [0, 1]);
        $this->assertInstanceOf(\Traversable::class, $collection);
        $services = iterator_to_array($collection);
        $this->assertSame([$service1, $service2], $services);
    }
}
