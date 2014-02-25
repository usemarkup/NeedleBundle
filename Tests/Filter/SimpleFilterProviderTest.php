<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\SimpleFilterProvider;

/**
 * Test for a simple filter provider implementation.
 */
class SimpleFilterProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->provider = new SimpleFilterProvider();
    }

    public function testIsFilterProvider()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Filter\FilterProviderInterface', $this->provider);
    }

    public function testGetFilterByName()
    {
        $name = 'i_am_a_filter';
        $filter = $this->provider->getFilterByName($name);
        $this->assertInstanceOf('Markup\NeedleBundle\Filter\FilterInterface', $filter);
        $this->assertEquals($name, $filter->getName());
    }
}
