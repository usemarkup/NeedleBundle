<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use Markup\NeedleBundle\Facet\SimpleFacetProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test for a simple facet provider implementation.
 */
class SimpleFacetProviderTest extends TestCase
{
    protected function setUp()
    {
        $this->provider = new SimpleFacetProvider();
    }

    public function testIsFacetProvider()
    {
        $this->assertInstanceOf(FacetProviderInterface::class, $this->provider);
    }

    public function testGetFacetByName()
    {
        $name = 'i_am_a_facet';
        $facet = $this->provider->getFacetByName($name);
        $this->assertInstanceOf(AttributeInterface::class, $facet);
        $this->assertEquals($name, $facet->getName());
    }
}
