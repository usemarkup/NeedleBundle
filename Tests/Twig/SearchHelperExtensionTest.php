<?php

namespace Markup\NeedleBundle\Tests\Twig;

use Markup\NeedleBundle\Twig\SearchHelperExtension;

/**
* A test for a twig extension that provides some helper functions/filters for search.
*/
class SearchHelperExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->extension = new SearchHelperExtension($this->container);
    }

    public function testIsTwigExtension()
    {
        $this->assertTrue($this->extension instanceof \Twig_ExtensionInterface);
    }

    public function testCanonicalizeValueForFacet()
    {
        $original = 'original';
        $canonicalized = 'canonicalized';
        $facet = $this->getMock('Markup\NeedleBundle\Facet\FacetInterface');
        $facetValueCanonicalizer = $this->getMock('Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface');
        $facetValueCanonicalizer
            ->expects($this->any())
            ->method('canonicalizeForFacet')
            ->with($this->equalTo($original), $this->equalTo($facet))
            ->will($this->returnValue($canonicalized));
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('markup_needle.facet.value_canonicalizer'))
            ->will($this->returnValue($facetValueCanonicalizer));
        $this->assertEquals($canonicalized, $this->extension->canonicalizeForFacet($original, $facet));
    }
}
