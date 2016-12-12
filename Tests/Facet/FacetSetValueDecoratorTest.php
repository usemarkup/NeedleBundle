<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetSetValueDecorator;

/**
* A test for a facet set decorator that applies decoration to facet values.
*/
class FacetSetValueDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->facetValueDecorator = $this->createMock('Markup\NeedleBundle\Facet\FacetValueDecoratorInterface');
        $this->decorator = new FacetSetValueDecorator($this->facetValueDecorator);
    }

    public function testIsFacetSetDecorator()
    {
        $this->assertTrue($this->decorator instanceof \Markup\NeedleBundle\Facet\FacetSetDecoratorInterface);
    }

    public function testDecoration()
    {
        $value = 'decorate me!';
        $facetValue = $this->createMock('Markup\NeedleBundle\Facet\FacetValueInterface');
        $facetValue
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($value));
        $facet = $this->createMock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $facetSet = $this->createMock('Markup\NeedleBundle\Facet\FacetSetInterface');
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
