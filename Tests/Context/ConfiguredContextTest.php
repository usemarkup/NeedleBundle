<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Attribute\Attribute;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Context\ConfiguredContext;
use Markup\NeedleBundle\Sort\RelevanceSort;
use Mockery as m;

class ConfiguredContextTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->config = m::mock('Markup\NeedleBundle\Config\ContextConfigurationInterface');
        $this->filterProvider = m::mock('Markup\NeedleBundle\Attribute\AttributeProviderInterface');
        $this->facetProvider = m::mock('Markup\NeedleBundle\Facet\FacetProviderInterface');
        $this->facetSetDecoratorProvider = m::mock('Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface');
        $this->facetCollatorProvider = m::mock('Markup\NeedleBundle\Collator\CollatorProviderInterface');
        $this->facetSortOrderProvider = m::mock('Markup\NeedleBundle\Facet\SortOrderProviderInterface');
        $this->interceptorProvider = m::mock('Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider');
        $this->context = new ConfiguredContext(
            $this->config,
            $this->filterProvider,
            $this->facetProvider,
            $this->facetSetDecoratorProvider,
            $this->facetCollatorProvider,
            $this->facetSortOrderProvider,
            $this->interceptorProvider
        );
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsContext()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Context\SearchContextInterface', $this->context);
    }

    public function testGetItemsPerPageWithPositiveNumber()
    {
        $itemsPerPage = 12;
        $this->config
            ->shouldReceive('getDefaultItemsPerPage')
            ->andReturn($itemsPerPage);
        $this->assertEquals($itemsPerPage, $this->context->getItemsPerPage());
    }

    public function testGetItemsPerPageWhenUnbounded()
    {
        $this->config
            ->shouldReceive('getDefaultItemsPerPage')
            ->andReturn(0);
        $this->assertNull($this->context->getItemsPerPage());
    }

    public function testGetFacets()
    {
        $facetNames = array('gender', 'size', 'price');
        $this->config
            ->shouldReceive('getDefaultFacetingAttributes')
            ->andReturn($facetNames);
        $facet = m::mock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $this->facetProvider
            ->shouldReceive('getFacetByName')
            ->andReturn($facet);
        $facets = $this->context->getFacets();
        $this->assertCount(count($facetNames), $facets);
        $this->assertContainsOnlyInstancesOf('Markup\NeedleBundle\Attribute\AttributeInterface', $facets);
    }

    public function testGetDefaultFilterQueries()
    {
        $this->filterProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $filterConfig = array(
            'active' => true,
            'in_stock' => true,
        );
        $this->config
            ->shouldReceive('getDefaultFilterQueries')
            ->andReturn($filterConfig);
        $queries = $this->context->getDefaultFilterQueries();
        $this->assertCount(2, $queries);
        $this->assertContainsOnlyInstancesOf('Markup\NeedleBundle\Filter\FilterQueryInterface', $queries);
        $firstQuery = $queries[0];
        $this->assertEquals('active', $firstQuery->getFilter()->getName());
    }

    public function testDefaultSortCollectionForQueryUsesSearchTermSortsWhenTreatedAsSearchTerm()
    {
        $this->filterProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $sortConfig = array(
            'relevance' => 'desc',
            'price' => 'desc',
        );
        $this->config
            ->shouldReceive('getDefaultSortsForSearchTermQuery')
            ->andReturn($sortConfig);
        $query = m::mock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $query
            ->shouldReceive('shouldTreatAsTextSearch')
            ->andReturn(true);
        $sorts = $this->context->getDefaultSortCollectionForQuery($query);
        $this->assertCount(2, $sorts);
        $this->assertEquals('price', $sorts->get(1)->getFilter()->getName());
    }

    public function testDefaultSortCollectionForQueryGetsRelevanceSortWhenRelevanceSelected()
    {
        $this->filterProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $sortConfig = array(
            ContextConfigurationInterface::SORT_RELEVANCE => ContextConfigurationInterface::ORDER_DESC,
        );
        $this->config
            ->shouldReceive('getDefaultSortsForSearchTermQuery')
            ->andReturn($sortConfig);
        $query = m::mock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $query
            ->shouldReceive('shouldTreatAsTextSearch')
            ->andReturn(true);
        $sorts = $this->context->getDefaultSortCollectionForQuery($query);
        $this->assertCount(1, $sorts);
        $this->assertEquals(RelevanceSort::RELEVANCE_FILTER_NAME, $sorts->first()->getFilter()->getName());
    }

    public function testDefaultSortCollectionForQueryUsesNonSearchTermWhenNotTreatedAsSearchTerm()
    {
        $this->filterProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $sortConfig = array(
            'name' => 'asc',
            'price' => 'desc',
        );
        $this->config
            ->shouldReceive('getDefaultSortsForNonSearchTermQuery')
            ->andReturn($sortConfig);
        $query = m::mock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $query
            ->shouldReceive('shouldTreatAsTextSearch')
            ->andReturn(false);
        $sorts = $this->context->getDefaultSortCollectionForQuery($query);
        $this->assertCount(2, $sorts);
        $this->assertFalse($sorts->first()->isDescending());
    }

    public function testSortsUsingListOfObjects()
    {
        $this->filterProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $sortConfig = array(
            array('name' => 'asc'),
            array('price' => 'desc'),
        );
        $this->config
            ->shouldReceive('getDefaultSortsForNonSearchTermQuery')
            ->andReturn($sortConfig);
        $query = m::mock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $query
            ->shouldReceive('shouldTreatAsTextSearch')
            ->andReturn(false);
        $sorts = $this->context->getDefaultSortCollectionForQuery($query);
        $this->assertCount(2, $sorts);
        $this->assertEquals('name', $sorts->first()->getFilter()->getName());
    }

    public function testBoostQueryFields()
    {
        $boosts = array(
            'name' => 5,
            'category' => 0.4,
        );
        $this->config
            ->shouldReceive('getDefaultBoosts')
            ->andReturn($boosts);
        $boostFields = $this->context->getBoostQueryFields();
        $this->assertCount(2, $boostFields);
        $this->assertContainsOnlyInstancesOf('Markup\NeedleBundle\Boost\BoostQueryFieldInterface', $boostFields);
        $firstBoostField = $boostFields[0];
        $this->assertEquals(5, $firstBoostField->getBoostFactor());
    }

    public function testGetAvailableFilterNames()
    {
        $filters = array('gender', 'size', 'on_sale');
        $this->config
            ->shouldReceive('getFilterableAttributes')
            ->andReturn($filters);
        $this->assertEquals($filters, $this->context->getAvailableFilterNames());
    }

    public function testWhetherFacetIgnoresCurrentFilters()
    {
        $facet = m::mock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $this->config
            ->shouldReceive('shouldIgnoreCurrentFilteredAttributesInFaceting')
            ->andReturn(true);
        $this->assertTrue($this->context->getWhetherFacetIgnoresCurrentFilters($facet));
    }

    public function testGetSetDecoratorForFacet()
    {
        $decorator = m::mock('Markup\NeedleBundle\Facet\FacetSetDecoratorInterface');
        $field = 'field';
        $facet = m::mock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $facet
            ->shouldReceive('getName')
            ->andReturn($field);
        $this->facetSetDecoratorProvider
            ->shouldReceive('getDecoratorForFacet')
            ->with($facet)
            ->andReturn($decorator);
        $this->assertSame($decorator, $this->context->getSetDecoratorForFacet($facet));
    }

    public function testGetFacetCollatorProvider()
    {
        $this->assertSame($this->facetCollatorProvider, $this->context->getFacetCollatorProvider());
    }

    public function testGetFacetSortOrderProvider()
    {
        $this->assertSame($this->facetSortOrderProvider, $this->context->getFacetSortOrderProvider());
    }

    public function testGetInterceptor()
    {
        $interceptor = m::mock('Markup\NeedleBundle\Intercept\InterceptorInterface');
        $config = array(
            'sale' => array(
                'terms' => array('sale'),
                'type' => 'route',
                'route' => 'sale',
            ),
        );
        $this->interceptorProvider
            ->shouldReceive('createInterceptor')
            ->with($config)
            ->andReturn($interceptor);
        $this->config
            ->shouldReceive('getIntercepts')
            ->andReturn($config);
        $this->assertSame($interceptor, $this->context->getInterceptor());
    }
}
