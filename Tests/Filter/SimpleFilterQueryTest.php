<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\SimpleFilterQuery;

/**
* Test for a filter query with a simple constructor that deals with scalars/ simple values.
*/
class SimpleFilterQueryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->key = 'key';
        $this->value = 'value';
        $this->filterQuery = new SimpleFilterQuery($this->key, $this->value);
    }

    public function testIsFilterQuery()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Filter\FilterQueryInterface', $this->filterQuery);
    }

    public function testGetSearchKey()
    {
        $this->assertEquals($this->key, $this->filterQuery->getSearchKey());
    }

    public function testGetSearchValue()
    {
        $this->assertEquals($this->value, $this->filterQuery->getSearchValue());
    }

    public function testGetFilter()
    {
        $filter = $this->filterQuery->getFilter();
        $this->assertInstanceOf('Markup\NeedleBundle\Attribute\AttributeInterface', $filter);
        $this->assertEquals($this->key, $filter->getSearchKey());
    }

    public function testGetFilterValue()
    {
        $filterValue = $this->filterQuery->getFilterValue();
        $this->assertInstanceOf('Markup\NeedleBundle\Filter\FilterValueInterface', $filterValue);
        $this->assertEquals($this->value, $filterValue->getSearchValue());
    }
}
