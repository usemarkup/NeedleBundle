<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetSetInterface;
use Markup\NeedleBundle\Facet\FacetValueInterface;
use Markup\NeedleBundle\Facet\MinCountFacetSetDecorator;
use PHPUnit\Framework\TestCase;

/**
* A test for a facet set decorator that filters out values that don't meet a minimum count.
*/
class MinCountFacetSetDecoratorTest extends TestCase
{
    /**
     * @var MinCountFacetSetDecorator
     */
    private $decorator;

    public function setUp()
    {
        $this->facetSet = $this->createMock(FacetSetInterface::class);
        $this->minCount = 4;
        $this->decorator = new MinCountFacetSetDecorator($this->minCount);
        $this->decorator->decorate($this->facetSet);
    }

    public function testFiltering()
    {
        $facetValue1 = $this->createMock(FacetValueInterface::class);
        $facetValue2 = $this->createMock(FacetValueInterface::class);
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
            ->will($this->returnValue(new \ArrayIterator([$facetValue1, $facetValue2])));
        $facetValues = iterator_to_array($this->decorator);
        $this->assertCount(1, $facetValues);
        foreach ($facetValues as $facetValue) {
            break;
        }
        $this->assertSame($facetValue2, $facetValue);
    }
}
