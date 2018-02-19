<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\FilterProviderInterface;
use Markup\NeedleBundle\Filter\NullFilterProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test for null filter provider.
 */
class NullFilterProviderTest extends TestCase
{
    /**
     * @var NullFilterProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = new NullFilterProvider();
    }

    public function testIsFilterProvider()
    {
        $this->assertInstanceOf(FilterProviderInterface::class, $this->provider);
    }

    public function testGetFilterByNameReturnsNull()
    {
        $this->assertNull($this->provider->getFilterByName('filter'));
    }
}
