<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\NullFilterProvider;

/**
 * Test for null filter provider.
 */
class NullFilterProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->provider = new NullFilterProvider();
    }

    public function testIsFilterProvider()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Filter\FilterProviderInterface', $this->provider);
    }

    public function testGetFilterByNameReturnsNull()
    {
        $this->assertNull($this->provider->getFilterByName('filter'));
    }
}
