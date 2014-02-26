<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\Filter;

/**
 * Test for filter implementation
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFilter()
    {
        $filter = new Filter('name');
        $this->assertInstanceOf('Markup\NeedleBundle\Filter\FilterInterface', $filter);
    }

    public function testIsAttribute()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Attribute\AttributeInterface', new Filter('name'));
    }

    public function testOutputsForOneWordName()
    {
        $name = 'filter';
        $filter = new Filter($name);
        $this->assertEquals('filter', $filter->getName());
        $this->assertEquals('Filter', $filter->getDisplayName());
        $this->assertEquals('filter', $filter->getSearchKey());
    }

    public function testOutputsForTwoWordName()
    {
        $name = 'sleeve_length';
        $filter = new Filter($name);
        $this->assertEquals('sleeve_length', $filter->getName());
        $this->assertEquals('Sleeve length', $filter->getDisplayName());
        $this->assertEquals('sleeve_length', $filter->getSearchKey());
    }

    public function testOutputsForDifferentNameAndKey()
    {
        $name = 'category';
        $key = 'category_key';
        $filter = new Filter($name, $key);
        $this->assertEquals($name, $filter->getName());
        $this->assertEquals('Category', $filter->getDisplayName());
        $this->assertEquals($key, $filter->getSearchKey());
    }

    public function testOutputsWithAllSpecified()
    {
        $name = 'name';
        $key = 'key';
        $display = 'display';
        $filter = new Filter($name, $key, $display);
        $this->assertEquals($name, $filter->getName());
        $this->assertEquals($display, $filter->getDisplayName());
        $this->assertEquals($key, $filter->getSearchKey());
    }
}
