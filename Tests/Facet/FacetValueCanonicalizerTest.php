<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetValueCanonicalizer;

/**
* A test for a canonicalizer for facet values.
*/
class FacetValueCanonicalizerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->canonicalizer = new FacetValueCanonicalizer();
    }

    public function testIsFacetValueCanonicalizer()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface', $this->canonicalizer);
    }

    public function testNoCanonicalizersMakesCanonicalizeReturnOriginal()
    {
        $value = 'value';
        $facet = $this->getMock('Markup\NeedleBundle\Facet\FacetInterface');
        $this->assertEquals($value, $this->canonicalizer->canonicalizeForFacet($value, $facet));
    }

    public function testAddedCanonicalizerUsedForCanonicalization()
    {
        $original = 'original';
        $canonicalized = 'canonicalized';
        $canonicalizer = $this->getMock('Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface');
        $facetName = 'facet';
        $facet = $this->getMock('Markup\NeedleBundle\Facet\FacetInterface');
        $facet
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($facetName));
        $this->canonicalizer->addCanonicalizerForFacetName($canonicalizer, $facetName);
        $canonicalizer
            ->expects($this->any())
            ->method('canonicalizeForFacet')
            ->with($this->equalTo($original), $this->equalTo($facet))
            ->will($this->returnValue($canonicalized));
        $this->assertEquals($canonicalized, $this->canonicalizer->canonicalizeForFacet($original, $facet));
    }
}
