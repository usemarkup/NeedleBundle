<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\FilterQuery;

/**
* A test for a filter query object.
*/
class FilterQueryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = $this->getMock('Markup\NeedleBundle\Filter\FilterInterface');
        $this->filterValue = $this->getMock('Markup\NeedleBundle\Filter\FilterValueInterface');
        $this->filterQuery = new FilterQuery($this->filter, $this->filterValue);
    }

    public function testIsFilterQuery()
    {
        $this->assertTrue($this->filterQuery instanceof \Markup\NeedleBundle\Filter\FilterQueryInterface);
    }

    public function testGetSearchKey()
    {
        $key = 'color';
        $this->filter
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue($key));
        $this->assertEquals($key, $this->filterQuery->getSearchKey());
    }

    public function testGetSearchValue()
    {
        $value = 'red';
        $this->filterValue
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue($value));
        $this->assertEquals($value, $this->filterQuery->getSearchValue());
    }

    public function testGetFilter()
    {
        $this->assertEquals($this->filter, $this->filterQuery->getFilter());
    }

    public function testGetFilterValue()
    {
        $this->assertEquals($this->filterValue, $this->filterQuery->getFilterValue());
    }
}
