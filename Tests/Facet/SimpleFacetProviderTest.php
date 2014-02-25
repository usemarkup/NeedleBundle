<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\SimpleFacetProvider;

/**
 * Test for a simple facet provider implementation.
 */
class SimpleFacetProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->provider = new SimpleFacetProvider();
    }

    public function testIsFacetProvider()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\FacetProviderInterface', $this->provider);
    }

    public function testGetFacetByName()
    {
        $name = 'i_am_a_facet';
        $facet = $this->provider->getFacetByName($name);
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\FacetInterface', $facet);
        $this->assertEquals($name, $facet->getName());
    }
}
