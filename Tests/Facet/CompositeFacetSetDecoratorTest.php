<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\CompositeFacetSetDecorator;

/**
* A test for a facet set decorator that composes other decorators.
*/
class CompositeFacetSetDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->decorator1 = $this->getMock('Markup\NeedleBundle\Facet\FacetSetDecoratorInterface');
        $this->decorator2 = $this->getMock('Markup\NeedleBundle\Facet\FacetSetDecoratorInterface');
        $this->composite = new CompositeFacetSetDecorator([$this->decorator1, $this->decorator2]);
    }

    public function testIsFacetSetDecorator()
    {
        $this->assertTrue($this->composite instanceof \Markup\NeedleBundle\Facet\FacetSetDecoratorInterface);
    }

    public function testDecorate()
    {
        $facetSet = $this->getMock('Markup\NeedleBundle\Facet\FacetSetInterface');
        $this->decorator1
            ->expects($this->once())
            ->method('decorate')
            ->with($this->equalTo($facetSet));
        $this->decorator2
            ->expects($this->once())
            ->method('decorate')
            ->with($this->equalTo($this->decorator1));
        $return = $this->composite->decorate($facetSet);
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\CompositeFacetSetDecorator', $return);
    }
}
