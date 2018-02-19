<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\FilterQuery;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a filter query object.
*/
class FilterQueryTest extends TestCase
{
    /**
     * @var AttributeInterface
     */
    private $filter;

    /**
     * @var FilterValueInterface
     */
    private $filterValue;

    /**
     * @var FilterQuery
     */
    private $filterQuery;

    protected function setUp()
    {
        $this->filter = $this->createMock(AttributeInterface::class);
        $this->filterValue = $this->createMock(FilterValueInterface::class);
        $this->filterQuery = new FilterQuery($this->filter, $this->filterValue);
    }

    public function testIsFilterQuery()
    {
        $this->assertInstanceOf(FilterQueryInterface::class, $this->filterQuery);
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
