<?php

namespace Markup\NeedleBundle\Tests\Query;

use Doctrine\Common\Collections\ArrayCollection;
use Markup\NeedleBundle\Attribute\Attribute;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Filter\FilterQuery;
use Markup\NeedleBundle\Filter\ScalarFilterValue;
use Markup\NeedleBundle\Filter\SimpleFilter;
use Markup\NeedleBundle\Query\ResolvedSelectQuery;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Sort\EmptySortCollection;
use Markup\NeedleBundle\Sort\Sort;
use Markup\NeedleBundle\Sort\SortCollection;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * A test for a select query interface.
 */
class ResolvedSelectQueryTest extends MockeryTestCase
{
    protected function setUp()
    {
        $this->query = m::mock(SelectQueryInterface::class);
        $this->context = m::mock(SearchContextInterface::class);
    }

    /**
     * @doesNotPerformAssertions
     */
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

        $this->assertInstanceOf(EmptySortCollection::class, $query->getSortCollection());
    }

    public function testGetSortCollectionWithoutContextReturnsDefaultCollection()
    {
        $sortCollection = new SortCollection();
        $sortCollection->add(new Sort(new Attribute('weight')));
        $sortCollection->add(new Sort(new Attribute('on_sale')));

        $this->query->shouldReceive('hasSortCollection')->andReturn(false);
        $this->context->shouldReceive('getDefaultSortCollectionForQuery')->andReturn($sortCollection);

        $query = new ResolvedSelectQuery($this->query, $this->context);

        $this->assertInstanceOf(SortCollection::class, $query->getSortCollection());
        $this->assertEquals(2, count($query->getSortCollection()));
    }

    public function testGetFilterQueriesWithoutContext()
    {
        $filterQueries = [
            new FilterQuery(new SimpleFilter('color'), new ScalarFilterValue('blue')),
            new FilterQuery(new SimpleFilter('size'), new ScalarFilterValue('xs')),
        ];

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

    public function testShouldNotUseFuzzyMatchingWithoutContext()
    {
        $this->assertFalse((new ResolvedSelectQuery($this->query))->shouldUseFuzzyMatching());
    }

    public function testShouldUseFuzzyMatchingIfContextSpecifies()
    {
        $this->context
            ->shouldReceive('shouldUseFuzzyMatching')
            ->andReturn(true);
        $this->assertTrue((new ResolvedSelectQuery($this->query, $this->context))->shouldUseFuzzyMatching());
    }
}
