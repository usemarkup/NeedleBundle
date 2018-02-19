<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\FilterProviderInterface;
use Markup\NeedleBundle\Filter\SimpleFilterProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test for a simple filter provider implementation.
 */
class SimpleFilterProviderTest extends TestCase
{
    /**
     * @var SimpleFilterProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = new SimpleFilterProvider();
    }

    public function testIsFilterProvider()
    {
        $this->assertInstanceOf(FilterProviderInterface::class, $this->provider);
    }

    public function testGetFilterByName()
    {
        $name = 'i_am_a_filter';
        $filter = $this->provider->getFilterByName($name);
        $this->assertInstanceOf(AttributeInterface::class, $filter);
        $this->assertEquals($name, $filter->getName());
    }
}
