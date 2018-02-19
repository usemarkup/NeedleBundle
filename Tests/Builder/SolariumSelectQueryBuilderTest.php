<?php

namespace Markup\NeedleBundle\Tests\Builder;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\Query\Query;

/**
 * A test for an object that can build a Solarium select query that maps a generic select search query.
 */
class SolariumSelectQueryBuilderTest extends TestCase
{
    /**
     * @var Client
     */
    private $solarium;

    /**
     * @var FilterQueryLucenifier
     */
    private $lucenifier;

    /**
     * @var SolariumSelectQueryBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->solarium = new Client();
        $this->lucenifier = $this->createMock(FilterQueryLucenifier::class);
        $this->builder = new SolariumSelectQueryBuilder($this->solarium, $this->lucenifier);
    }

    public function testBuildWithNoOperationsReturnsSolariumSelectQuery()
    {
        $genericQuery = $this->createMock(ResolvedSelectQueryInterface::class);
        $query = $this->builder->buildSolariumQueryFromGeneric($genericQuery);
        $this->assertInstanceOf(Query::class, $query);
    }

    public function testBuildWithSearchTerm()
    {
        $genericQuery = $this->createMock(ResolvedSelectQueryInterface::class);
        $term = 'pirates';
        $genericQuery
            ->expects($this->any())
            ->method('getSearchTerm')
            ->will($this->returnValue($term));
        $genericQuery
            ->expects($this->any())
            ->method('hasSearchTerm')
            ->will($this->returnValue(true));
        $query = $this->builder->buildSolariumQueryFromGeneric($genericQuery);
        $this->assertEquals($term, $query->getQuery());
    }

    public function testAddFilterQuery()
    {
        $genericQuery = $this->createMock(ResolvedSelectQueryInterface::class);
        $filterQuery = $this->createMock(FilterQueryInterface::class);
        $filterQuery
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue('color'));
        $filterQuery
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('red'));
        $this->lucenifier
            ->expects($this->any())
            ->method('lucenify')
            ->will($this->returnValue('color:"red"'));
        $filter = $this->createMock(AttributeInterface::class);
        $filterValue = $this->createMock(FilterValueInterface::class);
        $filter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('color_key'));
        $filterQuery
            ->expects($this->any())
            ->method('getFilter')
            ->will($this->returnValue($filter));
        $filterQuery
            ->expects($this->any())
            ->method('getFilterValue')
            ->will($this->returnValue($filterValue));
        $genericQuery
            ->expects($this->any())
            ->method('getFilterQueries')
            ->will($this->returnValue([$filterQuery]));
        $query = $this->builder->buildSolariumQueryFromGeneric($genericQuery);
        $filterQueries = $query->getFilterQueries();
        $this->assertCount(
            1,
            $filterQueries,
            'checking correct number of filter queries returned'
        );
        $this->assertContainsOnly(
            FilterQuery::class,
            $filterQueries,
            false,
            'checking returned filter queries only contain Solarium filter queries'
        );
        foreach ($filterQueries as $singleFilterQuery) {
            break;
        }
        $this->assertEquals(
            'color_key',
            $singleFilterQuery->getKey(),
            'checking filter query key is correct'
        );
        $this->assertEquals(
            'color:"red"',
            $singleFilterQuery->getQuery(),
            'checking filter query value (query) is correct'
        ); //quoting applied
    }
}
