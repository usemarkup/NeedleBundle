<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\RangeFacetConfigurationInterface;
use Markup\NeedleBundle\Facet\RangeFacetField;
use Markup\NeedleBundle\Facet\RangeFacetInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a range facet field implementation.
*/
class RangeFacetFieldTest extends TestCase
{
    /**
     * @var RangeFacetField
     */
    private $range;

    public function setUp()
    {
        $this->filter = $this->createMock(AttributeInterface::class);
        $this->rangeFacetConfig = $this->createMock(RangeFacetConfigurationInterface::class);
        $this->range = new RangeFacetField($this->filter, $this->rangeFacetConfig);
    }

    public function testIsRangeFacet()
    {
        $this->assertInstanceOf(RangeFacetInterface::class, $this->range);
    }

    public function testIsAttribute()
    {
        $this->assertInstanceOf(AttributeInterface::class, $this->range);
    }

    public function testGetRangeSize()
    {
        $rangeSize = 100;
        $this->rangeFacetConfig
            ->expects($this->any())
            ->method('getGap')
            ->will($this->returnValue($rangeSize));
        $this->assertSame($rangeSize, $this->range->getRangeSize());
    }

    public function testGetRangesStart()
    {
        $start = 50;
        $this->rangeFacetConfig
            ->expects($this->any())
            ->method('getStart')
            ->will($this->returnValue($start));
        $this->assertSame($start, $this->range->getRangesStart());
    }

    public function testGetRangesEnd()
    {
        $end = 1000;
        $this->rangeFacetConfig
            ->expects($this->any())
            ->method('getEnd')
            ->will($this->returnValue($end));
        $this->assertSame($end, $this->range->getRangesEnd());
    }

    public function testGetName()
    {
        $name = 'xyzzy';
        $this->filter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $this->assertEquals($name, $this->range->getName());
    }

    public function testGetDisplayName()
    {
        $display = 'Xyzzy';
        $this->filter
            ->expects($this->any())
            ->method('getDisplayName')
            ->will($this->returnValue($display));
        $this->assertEquals($display, $this->range->getDisplayName());
    }

    public function testGetSearchKey()
    {
        $key = 'xyzzy';
        $this->filter
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue($key));
        $this->assertEquals($key, $this->range->getSearchKey());
    }

    public function testCastToString()
    {
        $display = 'Xyzzy';
        $this->filter
            ->expects($this->any())
            ->method('getDisplayName')
            ->will($this->returnValue($display));
        $this->assertEquals($display, (string) $this->range);
    }
}
