<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\FilterQuery;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
* A test for a filter query object.
*/
class FilterQueryTest extends MockeryTestCase
{
    /**
     * @var AttributeInterface|m\MockInterface
     */
    private $filter;

    /**
     * @var FilterValueInterface|m\MockInterface
     */
    private $filterValue;

    /**
     * @var FilterQuery
     */
    private $filterQuery;

    protected function setUp()
    {
        $this->filter = m::mock(AttributeInterface::class);
        $this->filterValue = m::mock(FilterValueInterface::class);
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
            ->shouldReceive('getSearchKey')
            ->with(['prefer_parsed' => false])
            ->andReturn($key);
        $this->assertEquals($key, $this->filterQuery->getSearchKey());
    }

    public function testGetSearchValue()
    {
        $value = 'red';
        $this->filterValue
            ->shouldReceive('getSearchValue')
            ->andReturn($value);
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
