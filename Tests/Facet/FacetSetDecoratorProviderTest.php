<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetSetDecoratorProvider;

class FacetSetDecoratorProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->provider = new FacetSetDecoratorProvider();
    }

    public function testIsDecoratorProvider()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface', $this->provider);
    }

    public function testNullReturnedForUnknownFacet()
    {
        $facet = $this->getMock('Markup\NeedleBundle\Facet\FacetInterface');
        $facet
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('unknown'));
        $this->assertNull($this->provider->getDecoratorForFacet($facet));
    }

    public function testAddAndGetSetDecorator()
    {
        $facetSetDecorator = $this->getMock('Markup\NeedleBundle\Facet\FacetSetDecoratorInterface');
        $field = 'known';
        $facet = $this->getMock('Markup\NeedleBundle\Facet\FacetInterface');
        $facet
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($field));
        $this->provider->addDecorator($field, $facetSetDecorator);
        $this->assertSame($facetSetDecorator, $this->provider->getDecoratorForFacet($facet));
    }
}
