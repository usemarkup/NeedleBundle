<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetSet;
use Markup\NeedleBundle\Facet\FacetSetArrayIterator;

/**
* A test for a generic facet set.
*/
class FacetSetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->facet = $this->getMock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $this->facetValueIterator = $this->getMock('Markup\NeedleBundle\Facet\FacetSetIteratorInterface');
        $this->facetSet = new FacetSet($this->facet, $this->facetValueIterator);
    }

    public function testIsFacetSet()
    {
        $this->assertTrue($this->facetSet instanceof \Markup\NeedleBundle\Facet\FacetSetInterface);
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
        $facetValue = $this->getMock('Markup\NeedleBundle\Facet\FacetValueInterface');
        $facetValueIterator = new FacetSetArrayIterator(array($facetValue));
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
