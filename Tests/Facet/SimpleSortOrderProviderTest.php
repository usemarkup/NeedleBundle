<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetField;
use Markup\NeedleBundle\Facet\SimpleSortOrderProvider;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Filter\SimpleFilter;

class SimpleSortOrderProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->shouldDefaultToIndex = true;
        $this->exceptions = ['size'];
        $this->provider = new SimpleSortOrderProvider($this->shouldDefaultToIndex, $this->exceptions);
    }

    public function testIsSortOrderProvider()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\SortOrderProviderInterface', $this->provider);
    }

    public function testNonExceptionReturnsIndexIfSpecifiedAsDefault()
    {
        $this->assertEquals(SortOrderProviderInterface::SORT_BY_INDEX, $this->provider->getSortOrderForFacet($this->createFacetForName('section')));
    }

    public function testExceptionReturnsCountIfIndexSpecifiedAsDefault()
    {
        $this->assertEquals(SortOrderProviderInterface::SORT_BY_COUNT, $this->provider->getSortOrderForFacet($this->createFacetForName('size')));
    }

    public function testNonExceptionReturnsCountIfCountSpecifiedAsDefault()
    {
        $shouldDefaultToIndex = false;
        $exceptions = ['color'];
        $provider = new SimpleSortOrderProvider($shouldDefaultToIndex, $exceptions);
        $this->assertEquals(SortOrderProviderInterface::SORT_BY_COUNT, $provider->getSortOrderForFacet($this->createFacetForName('section')));
    }

    public function testExceptionReturnsIndexIfCountSpecifiedAsDefault()
    {
        $shouldDefaultToIndex = false;
        $exceptions = ['color'];
        $provider = new SimpleSortOrderProvider($shouldDefaultToIndex, $exceptions);
        $this->assertEquals(SortOrderProviderInterface::SORT_BY_INDEX, $provider->getSortOrderForFacet($this->createFacetForName('color')));
    }

    private function createFacetForName($name)
    {
        return new FacetField(new SimpleFilter($name));
    }
}
