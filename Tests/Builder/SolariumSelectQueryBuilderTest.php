<?php

namespace Markup\NeedleBundle\Tests\Builder;

use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;

/**
* A test for an object that can build a Solarium select query that maps a generic select search query.
*/
class SolariumSelectQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->solarium = new \Solarium\Client();
        $this->lucenifier = $this->getMockBuilder('Markup\NeedleBundle\Lucene\FilterQueryLucenifier')
            ->disableOriginalConstructor()
            ->getMock();
        $this->builder = new SolariumSelectQueryBuilder($this->solarium, $this->lucenifier);
    }

    public function testBuildWithNoOperationsReturnsSolariumSelectQuery()
    {
        $genericQuery = $this->getMock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $query = $this->builder->buildSolariumQueryFromGeneric($genericQuery);
        $this->assertTrue($query instanceof \Solarium\QueryType\Select\Query\Query);
    }

    public function testBuildWithSearchTerm()
    {
        $genericQuery = $this->getMock('Markup\NeedleBundle\Query\SelectQueryInterface');
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
        $genericQuery = $this->getMock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $filterQuery = $this->getMock('Markup\NeedleBundle\Filter\FilterQueryInterface');
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
        $filter = $this->getMock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $filterValue = $this->getMock('Markup\NeedleBundle\Filter\FilterValueInterface');
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
        $this->assertCount(1, $filterQueries, 'checking correct number of filter queries returned');
        $this->assertContainsOnly('Solarium\QueryType\Select\Query\FilterQuery', $filterQueries, false, 'checking returned filter queries only contain Solarium filter queries');
        foreach ($filterQueries as $singleFilterQuery) {
            break;
        }
        $this->assertEquals('color_key', $singleFilterQuery->getKey(), 'checking filter query key is correct');
        $this->assertEquals('color:"red"', $singleFilterQuery->getQuery(), 'checking filter query value (query) is correct'); //quoting applied
    }
}
