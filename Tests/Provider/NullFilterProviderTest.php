<?php

namespace Markup\NeedleBundle\Tests\Provider;

use Markup\NeedleBundle\Provider\NullFilterProvider;

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
        $this->assertInstanceOf('Markup\NeedleBundle\Provider\FilterProviderInterface', $this->provider);
    }

    public function testGetFilterByNameReturnsNull()
    {
        $this->assertNull($this->provider->getFilterByName('filter'));
    }
}
