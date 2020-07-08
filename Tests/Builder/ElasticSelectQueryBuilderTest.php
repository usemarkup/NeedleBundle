<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Builder;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Builder\ElasticSelectQueryBuilder;
use Markup\NeedleBundle\Builder\QueryBuildOptions;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Markup\NeedleBundle\Sort\EmptySortCollection;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticSelectQueryBuilderTest extends MockeryTestCase
{
    /**
     * @var ElasticSelectQueryBuilder
     */
    private $builder;

    protected function setUp()
    {
        $this->builder = new ElasticSelectQueryBuilder();
    }

    public function testBuildWithNoOperationsReturnsSolariumSelectQuery()
    {
        $genericQuery = m::spy(ResolvedSelectQueryInterface::class);
        $genericQuery->shouldReceive('getSortCollection')->andReturn(new EmptySortCollection());
        $query = $this->builder->buildElasticQueryFromGeneric($genericQuery, $this->emptyOptions());
        $this->assertEquals(new \stdClass(), $query['query']['match_all']);
    }

    public function testBuildWithSearchTerm()
    {
        $genericQuery = m::spy(ResolvedSelectQueryInterface::class);
        $genericQuery->shouldReceive('getSortCollection')->andReturn(new EmptySortCollection());

        $term = 'pirates';
        $genericQuery
            ->shouldReceive('getSearchTerm')
            ->andReturn($term);
        $genericQuery
            ->shouldReceive('hasSearchTerm')
            ->andReturn(true);
        $query = $this->builder->buildElasticQueryFromGeneric($genericQuery, $this->emptyOptions());
        $this->assertEquals($term, $query['query']['multi_match']['query']);
    }

    public function testBuildWithSearchTermThatRequiresEscaping()
    {
        $genericQuery = m::spy(ResolvedSelectQueryInterface::class);
        $genericQuery->shouldReceive('getSortCollection')->andReturn(new EmptySortCollection());

        $term = 'lemons::apples::oranges';
        $genericQuery
            ->shouldReceive('getSearchTerm')
            ->andReturn($term);
        $genericQuery
            ->shouldReceive('hasSearchTerm')
            ->andReturn(true);
        $query = $this->builder->buildElasticQueryFromGeneric($genericQuery, $this->emptyOptions());
        $this->assertEquals('lemons\:\:apples\:\:oranges', $query['query']['multi_match']['query']);
    }

    public function testAddFilterQuery()
    {
        $genericQuery = m::spy(ResolvedSelectQueryInterface::class);
        $genericQuery->shouldReceive('getSortCollection')->andReturn(new EmptySortCollection());

        $filterQuery = m::mock(FilterQueryInterface::class);
        $filterQuery
            ->shouldReceive('getSearchKey')
            ->andReturn('color');
        $filter = m::mock(AttributeInterface::class);
        $filterValue = m::mock(FilterValueInterface::class)
            ->shouldReceive('getValueType')
            ->andReturn(FilterValueInterface::TYPE_SIMPLE)
            ->shouldReceive('getSearchValue')
            ->andReturn('red')
            ->getMock();
        $filter
            ->shouldReceive('getName')
            ->andReturn('color_key');
        $filterQuery
            ->shouldReceive('getFilter')
            ->andReturn($filter);
        $filterQuery
            ->shouldReceive('getFilterValue')
            ->andReturn($filterValue);
        $genericQuery
            ->shouldReceive('getFilterQueries')
            ->andReturn([$filterQuery]);
        $query = $this->builder->buildElasticQueryFromGeneric($genericQuery, $this->emptyOptions());
        $filterQueries = array_map(
            function ($item) {
                return $item['term'];
            },
            array_slice($query['query']['constant_score']['filter']['bool']['must'], 1)
        );
        $this->assertCount(
            1,
            $filterQueries,
            'checking correct number of filter queries returned'
        );
        $this->assertEquals([['color' => 'red']], $filterQueries);
    }

    private function emptyOptions(): QueryBuildOptions
    {
        return new QueryBuildOptions();
    }
}
