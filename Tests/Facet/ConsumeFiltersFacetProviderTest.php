<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\ConsumeFiltersFacetProvider;
use Mockery as m;

/**
 * Test for a facet provider that consumes a filter provider.
 */
class ConsumeFiltersFacetProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->filterProvider = m::mock('Markup\NeedleBundle\Filter\FilterProviderInterface');
        $this->provider = new ConsumeFiltersFacetProvider($this->filterProvider);
    }

    public function testIsFacetProvider()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\FacetProviderInterface', $this->provider);
    }

    public function testGetFacetByKnownName()
    {
        $name = 'a_name';
        $filter = m::mock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $filter
            ->shouldReceive('getName')
            ->andReturn($name);
        $this->filterProvider
            ->shouldReceive('getFilterByName')
            ->with($name)
            ->andReturn($filter);
        $facet = $this->provider->getFacetByName($name);
        $this->assertInstanceOf('Markup\NeedleBundle\Attribute\AttributeInterface', $facet);
        $this->assertEquals($name, $facet->getName());
    }

    public function testGetFacetByUnknownNameReturnsNull()
    {
        $name = 'unknown';
        $this->filterProvider
            ->shouldReceive('getFilterByName')
            ->andReturn(null);
        $this->assertNull($this->provider->getFacetByName($name));
    }
}
