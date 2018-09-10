<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Exception\MissingAttributeException;
use Markup\NeedleBundle\Facet\ConsumeFiltersFacetProvider;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Test for a facet provider that consumes a filter provider.
 */
class ConsumeFiltersFacetProviderTest extends MockeryTestCase
{
    /**
     * @var AttributeProviderInterface|m\MockInterface
     */
    private $filterProvider;

    /**
     * @var ConsumeFiltersFacetProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->filterProvider = m::mock(AttributeProviderInterface::class);
        $this->provider = new ConsumeFiltersFacetProvider($this->filterProvider);
    }

    public function testIsFacetProvider()
    {
        $this->assertInstanceOf(FacetProviderInterface::class, $this->provider);
    }

    public function testGetFacetByKnownName()
    {
        $name = 'a_name';
        $filter = m::mock(AttributeInterface::class);
        $filter
            ->shouldReceive('getName')
            ->andReturn($name);
        $this->filterProvider
            ->shouldReceive('getAttributeByName')
            ->with($name)
            ->andReturn($filter);
        $facet = $this->provider->getFacetByName($name);
        $this->assertInstanceOf(AttributeInterface::class, $facet);
        $this->assertEquals($name, $facet->getName());
    }

    public function testGetFacetByUnknownNameReturnsNull()
    {
        $name = 'unknown';
        $this->filterProvider
            ->shouldReceive('getAttributeByName')
            ->andThrow(new MissingAttributeException());
        $this->assertNull($this->provider->getFacetByName($name));
    }
}
