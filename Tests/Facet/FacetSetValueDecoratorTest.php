<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetSetInterface;
use Markup\NeedleBundle\Facet\FacetSetValueDecorator;
use Markup\NeedleBundle\Facet\FacetValueDecoratorInterface;
use Markup\NeedleBundle\Facet\FacetValueInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a facet set decorator that applies decoration to facet values.
*/
class FacetSetValueDecoratorTest extends TestCase
{
    public function setUp()
    {
        $this->facetValueDecorator = $this->createMock(FacetValueDecoratorInterface::class);
        $this->decorator = new FacetSetValueDecorator($this->facetValueDecorator);
    }

    public function testIsFacetSetDecorator()
    {
        $this->assertTrue($this->decorator instanceof \Markup\NeedleBundle\Facet\FacetSetDecoratorInterface);
    }

    public function testDecoration()
    {
        $value = 'decorate me!';
        $facetValue = $this->createMock(FacetValueInterface::class);
        $facetValue
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($value));
        $facet = $this->createMock(AttributeInterface::class);
        $facetSet = $this->createMock(FacetSetInterface::class);
        $facetSet
            ->expects($this->any())
            ->method('getFacet')
            ->will($this->returnValue($facet));
        $facetSet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$facetValue])));
        $this->facetValueDecorator
            ->expects($this->once())
            ->method('decorate')
            ->with($this->equalTo($facetValue))
            ->will($this->returnValue($this->facetValueDecorator));
        $decoratedValue = 'i am decorated!';
        $this->facetValueDecorator
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($decoratedValue));
        $outputValues = [];
        $this->decorator->decorate($facetSet);
        foreach ($this->decorator as $decoratedFacetValue) {
            $outputValues[] = $decoratedFacetValue->getValue();
        }
        $this->assertEquals([$decoratedValue], $outputValues);
    }
}
