<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Context\UseAvailableFiltersAsFacetsContextDecorator;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a search context decorator that makes the context use the available filter names for facets.
*/
class UseAvailableFiltersAsFacetsContextDecoratorTest extends TestCase
{
    public function setUp()
    {
        $this->context = $this->createMock(SearchContextInterface::class);
        $this->facetProvider = $this->createMock(FacetProviderInterface::class);
        $this->decorator = new UseAvailableFiltersAsFacetsContextDecorator($this->context, $this->facetProvider);
    }

    public function testIsSearchContext()
    {
        $this->assertInstanceOf(SearchContextInterface::class, $this->decorator);
    }

    public function testGetAvailableFilterNames()
    {
        $filterNames = ['filter1', 'filter2', 'filter3'];
        $this->context
            ->expects($this->any())
            ->method('getAvailableFilterNames')
            ->will($this->returnValue($filterNames));
        $this->assertEquals($filterNames, $this->decorator->getAvailableFilterNames());
    }

    public function testGetFacets()
    {
        $facet = $this->createMock(AttributeInterface::class);
        $this->facetProvider
            ->expects($this->any())
            ->method('getFacetByName')
            ->will($this->returnValue($facet));
        $filterNames = ['filter1', 'filter2', 'filter3'];
        $this->context
            ->expects($this->any())
            ->method('getAvailableFilterNames')
            ->will($this->returnValue($filterNames));
        $facets = $this->decorator->getFacets();
        $this->assertCount(3, $facets);
        $this->assertContainsOnly(AttributeInterface::class, $facets);
    }

    //other methods simply delegate down, so skipping unit tests as little chance of regression
}
