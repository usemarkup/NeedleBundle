<?php

namespace Markup\NeedleBundle\Tests\Provider;

use Markup\NeedleBundle\Provider\ConsumeFiltersFacetProvider;
use Mockery as m;

/**
 * Test for a facet provider that consumes a filter provider.
 */
class ConsumeFiltersFacetProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->filterProvider = m::mock('Markup\NeedleBundle\Provider\FilterProviderInterface');
        $this->provider = new ConsumeFiltersFacetProvider($this->filterProvider);
    }

    public function testIsFacetProvider()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Provider\FacetProviderInterface', $this->provider);
    }

    public function testGetFacetByKnownName()
    {
        $name = 'a_name';
        $filter = m::mock('Markup\NeedleBundle\Filter\FilterInterface');
        $filter
            ->shouldReceive('getName')
            ->andReturn($name);
        $this->filterProvider
            ->shouldReceive('getFilterByName')
            ->with($name)
            ->andReturn($filter);
        $facet = $this->provider->getFacetByName($name);
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\FacetInterface', $facet);
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
