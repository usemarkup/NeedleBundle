<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\MinCountFacetSetDecorator;

/**
* A test for a facet set decorator that filters out values that don't meet a minimum count.
*/
class MinCountFacetSetDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->facetSet = $this->getMock('Markup\NeedleBundle\Facet\FacetSetInterface');
        $this->minCount = 4;
        $this->decorator = new MinCountFacetSetDecorator($this->minCount);
        $this->decorator->decorate($this->facetSet);
    }

    public function testFiltering()
    {
        $facetValue1 = $this->getMock('Markup\NeedleBundle\Facet\FacetValueInterface');
        $facetValue2 = $this->getMock('Markup\NeedleBundle\Facet\FacetValueInterface');
        $facetValue1
            ->expects($this->any())
            ->method('count')
            ->will($this->returnValue(3));
        $facetValue2
            ->expects($this->any())
            ->method('count')
            ->will($this->returnValue(4));
        $this->facetSet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array($facetValue1, $facetValue2))));
        $facetValues = iterator_to_array($this->decorator);
        $this->assertCount(1, $facetValues);
        foreach ($facetValues as $facetValue) {
            break;
        }
        $this->assertSame($facetValue2, $facetValue);
    }
}
