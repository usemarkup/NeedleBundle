<?php

namespace Markup\NeedleBundle\Tests\Facet;

use PHPUnit\Framework\TestCase;
use Markup\NeedleBundle\Facet\FacetValueInterface;
use Markup\NeedleBundle\Facet\MinCountFacetValueFilterIterator;

/**
* A test for a filter iterator that operates on an iteration of facet values and filters out values that don't have a minimum count.
*/
class MinCountFacetValueFilterIteratorTest extends TestCase
{
    public function testFilter()
    {
        $facetValue1 = $this->createMock(FacetValueInterface::class);
        $facetValue2 = $this->createMock(FacetValueInterface::class);
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
