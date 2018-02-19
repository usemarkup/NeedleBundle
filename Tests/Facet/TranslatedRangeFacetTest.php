<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\RangeFacetConfigurationInterface;
use Markup\NeedleBundle\Facet\RangeFacetInterface;
use Markup\NeedleBundle\Facet\TranslatedRangeFacet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

/**
* A test for a range facet that has a display name provided by a translator.
*/
class TranslatedRangeFacetTest extends TestCase
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RangeFacetConfigurationInterface
     */
    private $rangeFacetConfig;

    protected function setUp()
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->rangeFacetConfig = $this->createMock(RangeFacetConfigurationInterface::class);
    }

    public function testIsRangeFacet()
    {
        $translatedRangeFacet = new \ReflectionClass(TranslatedRangeFacet::class);
        $this->assertTrue($translatedRangeFacet->implementsInterface(RangeFacetInterface::class));
    }

    public function testIsAttribute()
    {
        $translatedRangeFacet = new \ReflectionClass(TranslatedRangeFacet::class);
        $this->assertTrue($translatedRangeFacet->implementsInterface(AttributeInterface::class));
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
