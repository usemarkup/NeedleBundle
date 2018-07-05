<?php

namespace Markup\NeedleBundle\Tests\Builder;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\AttributeSpecialization;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextInterface;
use Markup\NeedleBundle\Attribute\SpecializedAttribute;
use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;
use Markup\NeedleBundle\Exception\IllegalContextValueException;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Solarium\Client;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\Query\Query;

/**
 * A test for an object that can build a Solarium select query that maps a generic select search query.
 */
class SolariumSelectQueryBuilderTest extends MockeryTestCase
{
    /**
     * @var Client
     */
    private $solarium;

    /**
     * @var FilterQueryLucenifier|m\MockInterface
     */
    private $lucenifier;

    /**
     * @var SolariumSelectQueryBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->solarium = new Client();
        $this->lucenifier = m::mock(FilterQueryLucenifier::class);
        $this->builder = new SolariumSelectQueryBuilder($this->solarium, $this->lucenifier);
    }

    public function testBuildWithNoOperationsReturnsSolariumSelectQuery()
    {
        $genericQuery = m::spy(ResolvedSelectQueryInterface::class);
        $query = $this->builder->buildSolariumQueryFromGeneric($genericQuery);
        $this->assertInstanceOf(Query::class, $query);
    }

    public function testBuildWithSearchTerm()
    {
        $genericQuery = m::spy(ResolvedSelectQueryInterface::class);
        $term = 'pirates';
        $genericQuery
            ->shouldReceive('getSearchTerm')
            ->andReturn($term);
        $genericQuery
            ->shouldReceive('hasSearchTerm')
            ->andReturn(true);
        $query = $this->builder->buildSolariumQueryFromGeneric($genericQuery);
        $this->assertEquals($term, $query->getQuery());
    }

    public function testAddFilterQuery()
    {
        $genericQuery = m::spy(ResolvedSelectQueryInterface::class);
        $filterQuery = m::mock(FilterQueryInterface::class);
        $filterQuery
            ->shouldReceive('getSearchKey')
            ->andReturn('color');
        $filterQuery
            ->shouldReceive('getSearchValue')
            ->andReturn('red');
        $this->lucenifier
            ->shouldReceive('lucenify')
            ->andReturn('color:"red"');
        $filter = m::mock(AttributeInterface::class);
        $filterValue = m::mock(FilterValueInterface::class);
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

    public function testSetFieldsUsingAttributes()
    {
        $genericQuery = m::spy(ResolvedSelectQueryInterface::class);
        $stringFields = ['this', 'that'];
        $attributeSpecialization = new AttributeSpecialization('other');
        $attribute = new SpecializedAttribute(
            [$attributeSpecialization],
            'the'
        );
        $context = new class () implements AttributeSpecializationContextInterface {
            public function getValue()
            {
                return 'other';
            }

            public function getData()
            {
                return [];
            }
        };
        $attribute->setContext($context, 'other');
        $expectedFields = ['this', 'that', 'the_other'];
        $genericQuery
            ->shouldReceive('getFields')
            ->andReturn(array_merge($stringFields, [$attribute]));
        $query = $this->builder->buildSolariumQueryFromGeneric($genericQuery);
        $this->assertEquals($expectedFields, $query->getFields());
    }
}
