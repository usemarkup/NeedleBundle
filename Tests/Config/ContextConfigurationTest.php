<?php

namespace Markup\NeedleBundle\Tests\Config;

use Markup\NeedleBundle\Config\ContextConfiguration;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for a context configuration class.
 */
class ContextConfigurationTest extends TestCase
{
    public function testIsContextConfiguration()
    {
        $this->assertInstanceOf(ContextConfigurationInterface::class, new ContextConfiguration());
    }

    public function testEmptyConfigGivesDefault24ItemsPerPage()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(24, $config->getDefaultItemsPerPage());
    }

    public function testEmptyConfigGivesEmptyDefaultFilterQueries()
    {
        $config = new ContextConfiguration();
        $this->assertEquals([], $config->getDefaultFilterQueries());
    }

    public function testEmptyConfigGivesDefaultSearchTermSortsAsRelevanceDescending()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(
            [ContextConfigurationInterface::SORT_RELEVANCE => ContextConfigurationInterface::ORDER_DESC],
            $config->getDefaultSortsForSearchTermQuery()
        );
    }

    public function testEmptyConfigGivesDefaultNonSearchTermsSortsAsEmpty()
    {
        $config = new ContextConfiguration();
        $this->assertEquals([], $config->getDefaultSortsForNonSearchTermQuery());
    }

    public function testEmptyConfigGivesEmptyBoosts()
    {
        $config = new ContextConfiguration();
        $this->assertEquals([], $config->getDefaultBoosts());
    }

    public function testEmptyConfigGivesEmptyFacets()
    {
        $config = new ContextConfiguration();
        $this->assertEquals([], $config->getDefaultFacetingAttributes());
    }

    public function testEmptyConfigGivesEmptyIntercepts()
    {
        $config = new ContextConfiguration();
        $this->assertEquals([], $config->getIntercepts());
    }

    public function testEmptyConfigGivesEmptyFilterAttributesList()
    {
        $config = new ContextConfiguration();
        $this->assertEquals([], $config->getFilterableAttributes());
    }

    public function testEmptyConfigMeansShouldNotIgnoreCurrentFilteredAttributesInFaceting()
    {
        $config = new ContextConfiguration();
        $this->assertFalse($config->shouldIgnoreCurrentFilteredAttributesInFaceting());
    }

    public function testFullConfigGivesItemsPerPage()
    {
        $config = $this->createFullConfig();
        $this->assertEquals(12, $config->getDefaultItemsPerPage());
    }

    public function testFullConfigGivesDefaultFilterQueries()
    {
        $config = $this->createFullConfig();
        $this->assertEquals(['active' => true, 'in_stock' => true], $config->getDefaultFilterQueries());
    }

    public function testFullConfigGivesSortsForSearchTermsFromFallback()
    {
        $config = $this->createFullConfig();
        $this->assertEquals(
            ['name' => ContextConfigurationInterface::ORDER_ASC, 'price' => ContextConfigurationInterface::ORDER_DESC],
            $config->getDefaultSortsForSearchTermQuery()
        );
    }

    public function testFullConfigGivesSortsForNonSearchTerm()
    {
        $config = $this->createFullConfig();
        $this->assertEquals(
            ['velocity' => ContextConfigurationInterface::ORDER_DESC],
            $config->getDefaultSortsForNonSearchTermQuery()
        );
    }

    public function testFullConfigGivesBoosts()
    {
        $config = $this->createFullConfig();
        $this->assertEquals(['name' => 5, 'category' => 0.4], $config->getDefaultBoosts());
    }

    public function testFullConfigGivesFacets()
    {
        $config = $this->createFullConfig();
        $this->assertEquals(['gender', 'category', 'price'], $config->getDefaultFacetingAttributes());
    }

    public function testFullConfigGivesIntercepts()
    {
        $config = $this->createFullConfig();
        $this->assertEquals(
            [
                'sale' => [
                    'terms' => ['sale'],
                    'type' => 'route',
                    'route' => 'shop_sale',
                    'route_params' => [],
                ],
                '3xl' => [
                    'terms' => ['XXXL', '3XL'],
                    'type' => 'search',
                    'filters' => [
                        'size' => 'XXXL',
                    ],
                ],
            ],
            $config->getIntercepts()
        );
    }

    public function testFullConfigGivesFilterableAttributes()
    {
        $config = $this->createFullConfig();
        $this->assertEquals(['gender', 'color', 'size', 'on_sale'], $config->getFilterableAttributes());
    }

    public function testFullConfigGivesShouldIgnoreCurrentFiltersInFaceting()
    {
        $config = $this->createFullConfig();
        $this->assertTrue($config->shouldIgnoreCurrentFilteredAttributesInFaceting());
    }

    public function testFullConfigGivesShouldUseFuzzyMatching()
    {
        $config = $this->createFullConfig();
        $this->assertTrue($config->shouldUseFuzzyMatching());
    }

    /**
     * @return
     */
    private function getFullConfiguration()
    {
        return [
            'items_per_page' => 12,
            'base_filter_queries' => ['active' => true, 'in_stock' => true],
            'sorts' => ['name' => ContextConfigurationInterface::ORDER_ASC, 'price' => ContextConfigurationInterface::ORDER_DESC],
            'sorts_non_search_term' => ['velocity' => ContextConfigurationInterface::ORDER_DESC],
            'boosts' => ['name' => 5, 'category' => 0.4],
            'facets' => ['gender', 'category', 'price'],
            'intercepts' => [
                'sale' => [
                    'terms' => ['sale'],
                    'type' => 'route',
                    'route' => 'shop_sale',
                    'route_params' => [],
                ],
                '3xl' => [
                    'terms' => ['XXXL', '3XL'],
                    'type' => 'search',
                    'filters' => [
                        'size' => 'XXXL',
                    ],
                ],
            ],
            'filters' => ['gender', 'color', 'size', 'on_sale'],
            'should_ignore_current_filters_in_faceting' => true,
            'should_use_fuzzy_matching' => true,
        ];
    }

    private function createFullConfig(): ContextConfiguration
    {
        return new ContextConfiguration($this->getFullConfiguration());
    }
}
