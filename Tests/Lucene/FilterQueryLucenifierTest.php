<?php

namespace Markup\NeedleBundle\Tests\Lucene;

use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Mockery as m;

/**
* A test for a lucenifier that can operate on a filter query.
*/
class FilterQueryLucenifierTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->valueLucenifier = m::mock('Markup\NeedleBundle\Lucene\FilterValueLucenifier');
        $this->queryLucenifier = new FilterQueryLucenifier($this->valueLucenifier);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testLucenify()
    {
        $filterKey = 'search';
        $value = '42';
        $lucenifiedValue = 'fortytwo';
        $this->valueLucenifier
            ->shouldReceive('lucenify')
            ->andReturn($lucenifiedValue);
        $filterQuery = m::mock('Markup\NeedleBundle\Filter\FilterQueryInterface');
        $filterQuery
            ->shouldReceive('getSearchKey')
            ->andReturn($filterKey);
        $filterValue = m::mock('Markup\NeedleBundle\Filter\FilterValueInterface')->shouldIgnoreMissing();
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
        $filterQuery = m::mock('Markup\NeedleBundle\Filter\FilterQueryInterface');
        $filterQuery
            ->shouldReceive('getSearchKey')
            ->andReturn($filterKey);
        $filterValue = m::mock('Markup\NeedleBundle\Filter\FilterValueInterface')->shouldIgnoreMissing();
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
