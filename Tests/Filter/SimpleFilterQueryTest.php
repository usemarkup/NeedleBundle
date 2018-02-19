<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\SimpleFilterQuery;
use PHPUnit\Framework\TestCase;

/**
* Test for a filter query with a simple constructor that deals with scalars/ simple values.
*/
class SimpleFilterQueryTest extends TestCase
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var SimpleFilterQuery
     */
    private $filterQuery;

    public function setUp()
    {
        $this->key = 'key';
        $this->value = 'value';
        $this->filterQuery = new SimpleFilterQuery($this->key, $this->value);
    }

    public function testIsFilterQuery()
    {
        $this->assertInstanceOf(FilterQueryInterface::class, $this->filterQuery);
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
        $this->assertInstanceOf(AttributeInterface::class, $filter);
        $this->assertEquals($this->key, $filter->getSearchKey());
    }

    public function testGetFilterValue()
    {
        $filterValue = $this->filterQuery->getFilterValue();
        $this->assertInstanceOf(FilterValueInterface::class, $filterValue);
        $this->assertEquals($this->value, $filterValue->getSearchValue());
    }
}
