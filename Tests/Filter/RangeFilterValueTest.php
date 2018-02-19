<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\RangeFilterValue;
use Markup\NeedleBundle\Filter\RangeFilterValueInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a filter value that represents a range.
*/
class RangeFilterValueTest extends TestCase
{
    public function testIsRangeFilterValue()
    {
        $rangeValue = new \ReflectionClass(RangeFilterValue::class);
        $this->assertTrue($rangeValue->implementsInterface(RangeFilterValueInterface::class));
    }

    public function testGetSearchValue()
    {
        $min = 80.0;
        $max = 120.0;
        $range = new RangeFilterValue($min, $max);
        $this->assertEquals('[80 TO 120]', $range->getSearchValue());
    }

    public function testGetSlug()
    {
        $min = 80.0;
        $max = 120.0;
        $range = new RangeFilterValue($min, $max);
        $this->assertEquals('80-120', $range->getSlug());
    }

    public function testGetMin()
    {
        $min = 80.0;
        $max = 120.0;
        $range = new RangeFilterValue($min, $max);
        $this->assertEquals(80, $range->getMin());
    }

    public function testGetMax()
    {
        $min = 80.0;
        $max = 120.0;
        $range = new RangeFilterValue($min, $max);
        $this->assertEquals(120, $range->getMax());
    }
}
