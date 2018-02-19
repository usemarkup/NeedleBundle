<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\CompositeFacetSetDecorator;
use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;
use Markup\NeedleBundle\Facet\FacetSetInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a facet set decorator that composes other decorators.
*/
class CompositeFacetSetDecoratorTest extends TestCase
{
    public function setUp()
    {
        $this->decorator1 = $this->createMock(FacetSetDecoratorInterface::class);
        $this->decorator2 = $this->createMock(FacetSetDecoratorInterface::class);
        $this->composite = new CompositeFacetSetDecorator([$this->decorator1, $this->decorator2]);
    }

    public function testIsFacetSetDecorator()
    {
        $this->assertInstanceOf(FacetSetDecoratorInterface::class, $this->composite);
    }

    public function testDecorate()
    {
        $facetSet = $this->createMock(FacetSetInterface::class);
        $this->decorator1
            ->expects($this->once())
            ->method('decorate')
            ->with($this->equalTo($facetSet));
        $this->decorator2
            ->expects($this->once())
            ->method('decorate')
            ->with($this->equalTo($this->decorator1));
        $return = $this->composite->decorate($facetSet);
        $this->assertInstanceOf(CompositeFacetSetDecorator::class, $return);
    }
}
