<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\MinCountFacetValueFilterIterator;

/**
* A test for a filter iterator that operates on an iteration of facet values and filters out values that don't have a minimum count.
*/
class MinCountFacetValueFilterIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $facetValue1 = $this->getMock('Markup\NeedleBundle\Facet\FacetValueInterface');
        $facetValue2 = $this->getMock('Markup\NeedleBundle\Facet\FacetValueInterface');
        $facetValue1
            ->expects($this->any())
            ->method('count')
            ->will($this->returnValue(2));
        $facetValue2
            ->expects($this->any())
            ->method('count')
            ->will($this->returnValue(3));
        $facetValueIterator = new \ArrayIterator([$facetValue1, $facetValue2]);
        $filteredFacetValues = new MinCountFacetValueFilterIterator(3, $facetValueIterator);
        $this->assertCount(1, $filteredFacetValues);
        foreach ($filteredFacetValues as $filteredFacetValue) {
            break;
        }
        $this->assertSame($facetValue2, $filteredFacetValue);
    }
}
