<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Attribute\Attribute;
use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Boost\BoostQueryFieldInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Context\ConfiguredContext;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider;
use Markup\NeedleBundle\Intercept\InterceptorInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Sort\RelevanceSort;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ConfiguredContextTest extends MockeryTestCase
{
    /**
     * @var ContextConfigurationInterface|m\MockInterface
     */
    private $config;

    /**
     * @var ConfiguredContext
     */
    private $context;

    protected function setUp()
    {
        $this->config = m::mock(ContextConfigurationInterface::class);
        $this->attributeProvider = m::mock(AttributeProviderInterface::class);
        $this->facetProvider = m::mock(FacetProviderInterface::class);
        $this->facetSetDecoratorProvider = m::mock(FacetSetDecoratorProviderInterface::class);
        $this->facetCollatorProvider = m::mock(CollatorProviderInterface::class);
        $this->facetSortOrderProvider = m::mock(SortOrderProviderInterface::class);
        $this->interceptorProvider = m::mock(ConfiguredInterceptorProvider::class);
        $this->context = new ConfiguredContext(
            $this->config,
            $this->attributeProvider,
            $this->facetProvider,
            $this->facetSetDecoratorProvider,
            $this->facetCollatorProvider,
            $this->facetSortOrderProvider,
            $this->interceptorProvider
        );
    }

    public function testIsContext()
    {
        $this->assertInstanceOf(SearchContextInterface::class, $this->context);
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
        $facetNames = ['gender', 'size', 'price'];
        $this->config
            ->shouldReceive('getDefaultFacetingAttributes')
            ->andReturn($facetNames);
        $facet = m::mock(AttributeInterface::class);
        $this->facetProvider
            ->shouldReceive('getFacetByName')
            ->andReturn($facet);
        $facets = $this->context->getFacets();
        $this->assertCount(count($facetNames), $facets);
        $this->assertContainsOnlyInstancesOf(AttributeInterface::class, $facets);
    }

    public function testGetDefaultFilterQueries()
    {
        $this->attributeProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $filterConfig = [
            'active' => true,
            'in_stock' => true,
        ];
        $this->config
            ->shouldReceive('getDefaultFilterQueries')
            ->andReturn($filterConfig);
        $queries = $this->context->getDefaultFilterQueries();
        $this->assertCount(2, $queries);
        $this->assertContainsOnlyInstancesOf(FilterQueryInterface::class, $queries);
        $firstQuery = $queries[0];
        $this->assertEquals('active', $firstQuery->getFilter()->getName());
    }

    public function testDefaultSortCollectionForQueryUsesSearchTermSortsWhenTreatedAsSearchTerm()
    {
        $this->attributeProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $sortConfig = [
            'relevance' => 'desc',
            'price' => 'desc',
        ];
        $this->config
            ->shouldReceive('getDefaultSortsForSearchTermQuery')
            ->andReturn($sortConfig);
        $query = m::mock(SelectQueryInterface::class);
        $query
            ->shouldReceive('shouldTreatAsTextSearch')
            ->andReturn(true);
        $sorts = $this->context->getDefaultSortCollectionForQuery($query);
        $this->assertCount(2, $sorts);
        $this->assertEquals('price', $sorts->get(1)->getFilter()->getName());
    }

    public function testDefaultSortCollectionForQueryGetsRelevanceSortWhenRelevanceSelected()
    {
        $this->attributeProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $sortConfig = [
            ContextConfigurationInterface::SORT_RELEVANCE => ContextConfigurationInterface::ORDER_DESC,
        ];
        $this->config
            ->shouldReceive('getDefaultSortsForSearchTermQuery')
            ->andReturn($sortConfig);
        $query = m::mock(SelectQueryInterface::class);
        $query
            ->shouldReceive('shouldTreatAsTextSearch')
            ->andReturn(true);
        $sorts = $this->context->getDefaultSortCollectionForQuery($query);
        $this->assertCount(1, $sorts);
        $this->assertEquals(RelevanceSort::RELEVANCE_FILTER_NAME, $sorts->first()->getFilter()->getName());
    }

    public function testDefaultSortCollectionForQueryUsesNonSearchTermWhenNotTreatedAsSearchTerm()
    {
        $this->attributeProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $sortConfig = [
            'name' => 'asc',
            'price' => 'desc',
        ];
        $this->config
            ->shouldReceive('getDefaultSortsForNonSearchTermQuery')
            ->andReturn($sortConfig);
        $query = m::mock(SelectQueryInterface::class);
        $query
            ->shouldReceive('shouldTreatAsTextSearch')
            ->andReturn(false);
        $sorts = $this->context->getDefaultSortCollectionForQuery($query);
        $this->assertCount(2, $sorts);
        $this->assertFalse($sorts->first()->isDescending());
    }

    public function testSortsUsingListOfObjects()
    {
        $this->attributeProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $sortConfig = [
            ['name' => 'asc'],
            ['price' => 'desc'],
        ];
        $this->config
            ->shouldReceive('getDefaultSortsForNonSearchTermQuery')
            ->andReturn($sortConfig);
        $query = m::mock(SelectQueryInterface::class);
        $query
            ->shouldReceive('shouldTreatAsTextSearch')
            ->andReturn(false);
        $sorts = $this->context->getDefaultSortCollectionForQuery($query);
        $this->assertCount(2, $sorts);
        $this->assertEquals('name', $sorts->first()->getFilter()->getName());
    }

    public function testBoostQueryFields()
    {
        $boosts = [
            'name' => 5,
            'category' => 0.4,
        ];
        $this->config
            ->shouldReceive('getDefaultBoosts')
            ->andReturn($boosts);
        $this->attributeProvider
            ->shouldReceive('getAttributeByName')
            ->andReturnUsing(function ($name) {
                return new Attribute($name);
            });
        $boostFields = $this->context->getBoostQueryFields();
        $this->assertCount(2, $boostFields);
        $this->assertContainsOnlyInstancesOf(BoostQueryFieldInterface::class, $boostFields);
        $firstBoostField = $boostFields[0];
        $this->assertEquals(5, $firstBoostField->getBoostFactor());
    }

    public function testGetAvailableFilterNames()
    {
        $filters = ['gender', 'size', 'on_sale'];
        $this->config
            ->shouldReceive('getFilterableAttributes')
            ->andReturn($filters);
        $this->assertEquals($filters, $this->context->getAvailableFilterNames());
    }

    public function testWhetherFacetIgnoresCurrentFilters()
    {
        $facet = m::mock(AttributeInterface::class);
        $this->config
            ->shouldReceive('shouldIgnoreCurrentFilteredAttributesInFaceting')
            ->andReturn(true);
        $this->assertTrue($this->context->getWhetherFacetIgnoresCurrentFilters($facet));
    }

    public function testGetSetDecoratorForFacet()
    {
        $decorator = m::mock(FacetSetDecoratorInterface::class);
        $field = 'field';
        $facet = m::mock(AttributeInterface::class);
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
        $interceptor = m::mock(InterceptorInterface::class);
        $config = [
            'sale' => [
                'terms' => ['sale'],
                'type' => 'route',
                'route' => 'sale',
            ],
        ];
        $this->interceptorProvider
            ->shouldReceive('createInterceptor')
            ->with($config)
            ->andReturn($interceptor);
        $this->config
            ->shouldReceive('getIntercepts')
            ->andReturn($config);
        $this->assertSame($interceptor, $this->context->getInterceptor());
    }

    public function testShouldUseFuzzyMatching()
    {
        $shouldMatch = true;
        $this->config
            ->shouldReceive('shouldUseFuzzyMatching')
            ->andReturn($shouldMatch);
        $this->assertEquals($shouldMatch, $this->context->shouldUseFuzzyMatching());
    }
}
