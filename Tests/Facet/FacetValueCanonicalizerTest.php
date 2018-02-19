<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizer;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a canonicalizer for facet values.
*/
class FacetValueCanonicalizerTest extends TestCase
{
    /**
     * @var FacetValueCanonicalizer
     */
    private $canonicalizer;

    public function setUp()
    {
        $this->canonicalizer = new FacetValueCanonicalizer();
    }

    public function testIsFacetValueCanonicalizer()
    {
        $this->assertInstanceOf(FacetValueCanonicalizerInterface::class, $this->canonicalizer);
    }

    public function testNoCanonicalizersMakesCanonicalizeReturnOriginal()
    {
        $value = 'value';
        $facet = $this->createMock(AttributeInterface::class);
        $this->assertEquals($value, $this->canonicalizer->canonicalizeForFacet($value, $facet));
    }

    public function testAddedCanonicalizerUsedForCanonicalization()
    {
        $original = 'original';
        $canonicalized = 'canonicalized';
        $canonicalizer = $this->createMock(FacetValueCanonicalizerInterface::class);
        $facetName = 'facet';
        $facet = $this->createMock(AttributeInterface::class);
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
