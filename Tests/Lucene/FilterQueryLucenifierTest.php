<?php

namespace Markup\NeedleBundle\Tests\Lucene;

use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Markup\NeedleBundle\Lucene\FilterValueLucenifier;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
* A test for a lucenifier that can operate on a filter query.
*/
class FilterQueryLucenifierTest extends MockeryTestCase
{
    protected function setUp()
    {
        $this->valueLucenifier = m::mock(FilterValueLucenifier::class);
        $this->queryLucenifier = new FilterQueryLucenifier($this->valueLucenifier);
    }

    public function testLucenify()
    {
        $filterKey = 'search';
        $value = '42';
        $lucenifiedValue = 'fortytwo';
        $this->valueLucenifier
            ->shouldReceive('lucenify')
            ->andReturn($lucenifiedValue);
        $filterQuery = m::mock(FilterQueryInterface::class);
        $filterQuery
            ->shouldReceive('getSearchKey')
            ->andReturn($filterKey);
        $filterValue = m::mock(FilterValueInterface::class)->shouldIgnoreMissing();
        $filterValue
            ->shouldReceive('getSearchValue')
            ->andReturn($value);
        $filterQuery
            ->shouldReceive('getFilterValue')
            ->andReturn($filterValue);
        $expectedLucene = 'search:fortytwo';
        $this->assertEquals($expectedLucene, $this->queryLucenifier->lucenify($filterQuery));
    }

    public function testLucenifyWithEmptyValue()
    {
        $filterKey = 'search';
        $value = '';
        $this->valueLucenifier
            ->shouldReceive('lucenify')
            ->andReturn('');
        $filterQuery = m::mock(FilterQueryInterface::class);
        $filterQuery
            ->shouldReceive('getSearchKey')
            ->andReturn($filterKey);
        $filterValue = m::mock(FilterValueInterface::class)->shouldIgnoreMissing();
        $filterValue
            ->shouldReceive('getSearchValue')
            ->andReturn($value);
        $filterQuery
            ->shouldReceive('getFilterValue')
            ->andReturn($filterValue);
        $expectedLucene = '-search:[* TO *]';
        $this->assertEquals($expectedLucene, $this->queryLucenifier->lucenify($filterQuery));
    }
}
