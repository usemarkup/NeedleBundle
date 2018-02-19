<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\SimpleFilter;
use PHPUnit\Framework\TestCase;

/**
* A test for a simple named filter.
*/
class SimpleFilterTest extends TestCase
{
    public function testIsAttribute()
    {
        $this->assertInstanceOf(AttributeInterface::class, new SimpleFilter('name'));
    }

    public function testOutputsForOneWordName()
    {
        $key = 'filter';
        $filter = new SimpleFilter($key);
        $this->assertEquals('filter', $filter->getName());
        $this->assertEquals('Filter', $filter->getDisplayName());
        $this->assertEquals('filter', $filter->getSearchKey());
    }

    public function testOutputsForTwoWordName()
    {
        $key = 'sleeve_length';
        $filter = new SimpleFilter($key);
        $this->assertEquals('sleeve_length', $filter->getName());
        $this->assertEquals('Sleeve length', $filter->getDisplayName());
        $this->assertEquals('sleeve_length', $filter->getSearchKey());
    }


}
