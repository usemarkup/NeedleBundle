<?php

namespace Markup\NeedleBundle\Tests\Twig;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface;
use Markup\NeedleBundle\Twig\SearchHelperExtension;

/**
* A test for a twig extension that provides some helper functions/filters for search.
*/
class SearchHelperExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FacetValueCanonicalizerInterface
     */
    private $canonicalizer;

    /**
     * @var SearchHelperExtension
     */
    private $extension;

    protected function setUp()
    {
        $this->canonicalizer = $this->createMock(FacetValueCanonicalizerInterface::class);
        $this->extension = new SearchHelperExtension($this->canonicalizer);
    }

    public function testIsTwigExtension()
    {
        $this->assertInstanceOf(\Twig_ExtensionInterface::class, $this->extension);
    }

    public function testCanonicalizeValueForFacet()
    {
        $original = 'original';
        $canonicalized = 'canonicalized';
        $facet = $this->createMock(AttributeInterface::class);
        $this->canonicalizer
            ->expects($this->any())
            ->method('canonicalizeForFacet')
            ->with($this->equalTo($original), $this->equalTo($facet))
            ->will($this->returnValue($canonicalized));
        $this->assertEquals($canonicalized, $this->extension->canonicalizeForFacet($original, $facet));
    }
}
