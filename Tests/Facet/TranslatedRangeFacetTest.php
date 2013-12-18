<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\TranslatedRangeFacet;

/**
* A test for a range facet that has a display name provided by a translator.
*/
class TranslatedRangeFacetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->rangeFacetConfig = $this->getMock('Markup\NeedleBundle\Facet\RangeFacetConfigurationInterface');
    }

    public function testIsRangeFacet()
    {
        $translatedRangeFacet = new \ReflectionClass('Markup\NeedleBundle\Facet\TranslatedRangeFacet');
        $this->assertTrue($translatedRangeFacet->implementsInterface('Markup\NeedleBundle\Facet\RangeFacetInterface'));
    }

    public function testGetName()
    {
        $name = 'price';
        $facet = new TranslatedRangeFacet($name, $this->rangeFacetConfig, $this->translator, 'subdomain');
        $this->assertEquals($name, $facet->getName());
    }

    public function testGetRangeSize()
    {
        $rangeSize = 100;
        $this->rangeFacetConfig
            ->expects($this->any())
            ->method('getGap')
            ->will($this->returnValue($rangeSize));
        $facet = new TranslatedRangeFacet('facet', $this->rangeFacetConfig, $this->translator, 'subdomain');
        $this->assertSame($rangeSize, $facet->getRangeSize());
    }

    public function testGetRangesStart()
    {
        $start = 50;
        $this->rangeFacetConfig
            ->expects($this->any())
            ->method('getStart')
            ->will($this->returnValue($start));
        $facet = new TranslatedRangeFacet('facet', $this->rangeFacetConfig, $this->translator, 'subdomain');
        $this->assertSame($start, $facet->getRangesStart());
    }

    public function testGetRangesEnd()
    {
        $end = 1000;
        $this->rangeFacetConfig
            ->expects($this->any())
            ->method('getEnd')
            ->will($this->returnValue($end));
        $facet = new TranslatedRangeFacet('facet', $this->rangeFacetConfig, $this->translator, 'subdomain');
        $this->assertSame($end, $facet->getRangesEnd());
    }
}
