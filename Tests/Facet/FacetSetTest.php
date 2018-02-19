<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetSet;
use Markup\NeedleBundle\Facet\FacetSetArrayIterator;
use Markup\NeedleBundle\Facet\FacetSetInterface;
use Markup\NeedleBundle\Facet\FacetSetIteratorInterface;
use Markup\NeedleBundle\Facet\FacetValueInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a generic facet set.
*/
class FacetSetTest extends TestCase
{
    protected function setUp()
    {
        $this->facet = $this->createMock(AttributeInterface::class);
        $this->facetValueIterator = $this->createMock(FacetSetIteratorInterface::class);
        $this->facetSet = new FacetSet($this->facet, $this->facetValueIterator);
    }

    public function testIsFacetSet()
    {
        $this->assertInstanceOf(FacetSetInterface::class, $this->facetSet);
    }

    public function testGetFacet()
    {
        $facetName = 'color';
        $this->facet
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($facetName));
        $facet = $this->facetSet->getFacet();
        $this->assertEquals($facetName, $facet->getName());
    }

    public function testGetIterator()
    {
        $facetValue = $this->createMock(FacetValueInterface::class);
        $facetValueIterator = new FacetSetArrayIterator([$facetValue]);
        $facetSet = new FacetSet($this->facet, $facetValueIterator);
        foreach ($facetSet as $emittedFacetValue) {
            break;
        }
        $this->assertSame($facetValue, $emittedFacetValue);
    }

    public function testCount()
    {
        $count = 42;
        $this->facetValueIterator
            ->expects($this->any())
            ->method('count')
            ->will($this->returnValue(42));
        $this->assertEquals($count, count($this->facetSet));
    }
}
