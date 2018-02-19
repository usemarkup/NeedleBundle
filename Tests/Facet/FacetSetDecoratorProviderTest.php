<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProvider;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use PHPUnit\Framework\TestCase;

class FacetSetDecoratorProviderTest extends TestCase
{
    /**
     * @var FacetSetDecoratorProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = new FacetSetDecoratorProvider();
    }

    public function testIsDecoratorProvider()
    {
        $this->assertInstanceOf(FacetSetDecoratorProviderInterface::class, $this->provider);
    }

    public function testNullReturnedForUnknownFacet()
    {
        $facet = $this->createMock(AttributeInterface::class);
        $facet
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('unknown'));
        $this->assertNull($this->provider->getDecoratorForFacet($facet));
    }

    public function testAddAndGetSetDecorator()
    {
        $facetSetDecorator = $this->createMock(FacetSetDecoratorInterface::class);
        $field = 'known';
        $facet = $this->createMock(AttributeInterface::class);
        $facet
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($field));
        $this->provider->addDecorator($field, $facetSetDecorator);
        $this->assertSame($facetSetDecorator, $this->provider->getDecoratorForFacet($facet));
    }
}
