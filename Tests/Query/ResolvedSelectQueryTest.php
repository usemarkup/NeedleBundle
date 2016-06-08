<?php

namespace Markup\NeedleBundle\Tests\Query;

use Doctrine\Common\Collections\ArrayCollection;
use Markup\NeedleBundle\Attribute\Attribute;
use Markup\NeedleBundle\Filter\FilterQuery;
use Markup\NeedleBundle\Filter\ScalarFilterValue;
use Markup\NeedleBundle\Filter\SimpleFilter;
use Markup\NeedleBundle\Query\ResolvedSelectQuery;
use Markup\NeedleBundle\Sort\Sort;
use Markup\NeedleBundle\Sort\SortCollection;
use Mockery as m;

/**
 * A test for a select query interface.
 */
class ResolvedSelectQueryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->query = m::mock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $this->context = m::mock('Markup\NeedleBundle\Context\SearchContextInterface');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testCanInstantiate()
    {
        $query = new ResolvedSelectQuery($this->query);
        $query = new ResolvedSelectQuery($this->query, $this->context);
    }

    public function testGetSortCollectionWithoutContextReturnsEmptyCollection()
    {
        $this->query->shouldReceive('getSortCollection')->andReturn(null);
        $this->query->shouldReceive('hasSortCollection')->andReturn(false);
        $query = new ResolvedSelectQuery($this->query);

        $this->assertInstanceOf('Markup\NeedleBundle\Sort\EmptySortCollection', $query->getSortCollection());
    }

    public function testGetSortCollectionWithoutContextReturnsDefaultCollection()
    {
        $sortCollection = new SortCollection();
        $sortCollection->add(new Sort(new Attribute('weight')));
        $sortCollection->add(new Sort(new Attribute('on_sale')));

        $this->query->shouldReceive('hasSortCollection')->andReturn(null);
        $this->context->shouldReceive('getDefaultSortCollectionForQuery')->andReturn($sortCollection);

        $query = new ResolvedSelectQuery($this->query, $this->context);

        $this->assertInstanceOf('Markup\NeedleBundle\Sort\SortCollection', $query->getSortCollection());
        $this->assertEquals(2, count($query->getSortCollection()));
    }

    public function testGetFilterQueriesWithoutContext()
    {
        $filterQueries = new ArrayCollection();
        $filterQueries->add(new FilterQuery(new SimpleFilter('color'), new ScalarFilterValue('blue')));
        $filterQueries->add(new FilterQuery(new SimpleFilter('size'), new ScalarFilterValue('xs')));

        $this->query->shouldReceive('getFilterQueries')->andReturn($filterQueries);

        $query = new ResolvedSelectQuery($this->query);

        $this->assertEquals(2, count($query->getFilterQueries()));
    }

    public function testGetFilterQueriesWithContext()
    {
        $filterQueries = [];
        $filterQueries[] = new FilterQuery(new SimpleFilter('color'), new ScalarFilterValue('blue'));
        $filterQueries[] = new FilterQuery(new SimpleFilter('size'), new ScalarFilterValue('xs'));

        $defaultFilterQueries = [];
        $defaultFilterQueries[] = new FilterQuery(new SimpleFilter('collection'), new ScalarFilterValue('tall_and_large'));

        $this->query->shouldReceive('getFilterQueries')->andReturn($filterQueries);
        $this->context->shouldReceive('getDefaultFilterQueries')->andReturn($defaultFilterQueries);

        $query = new ResolvedSelectQuery($this->query, $this->context);

        $this->assertEquals(3, count($query->getFilterQueries()));
    }

    public function testGetOriginalSelectQuery()
    {
        $this->assertSame($this->query, (new ResolvedSelectQuery($this->query))->getOriginalSelectQuery());
    }
}
