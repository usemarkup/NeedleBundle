<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\SimpleFilter;

/**
* A test for a simple named filter.
*/
class SimpleFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFilter()
    {
        $filter = new SimpleFilter('name');
        $this->assertTrue($filter instanceof \Markup\NeedleBundle\Filter\FilterInterface);
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
