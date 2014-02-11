<?php

namespace Markup\NeedleBundle\Tests\Config;

use Markup\NeedleBundle\Config\ContextConfiguration;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;

/**
 * Test for a context configuration class.
 */
class ContextConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testIsContextConfiguration()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Config\ContextConfigurationInterface', new ContextConfiguration());
    }

    public function testEmptyConfigGivesDefault24ItemsPerPage()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(24, $config->getDefaultItemsPerPage());
    }

    public function testEmptyConfigGivesEmptyDefaultFilterQueries()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(array(), $config->getDefaultFilterQueries());
    }

    public function testEmptyConfigGivesDefaultSearchTermSortsAsRelevanceDescending()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(
            array(ContextConfigurationInterface::SORT_RELEVANCE => ContextConfigurationInterface::ORDER_DESC),
            $config->getDefaultSortsForSearchTermQuery()
        );
    }

    public function testEmptyConfigGivesDefaultNonSearchTermsSortsAsEmpty()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(array(), $config->getDefaultSortsForNonSearchTermQuery());
    }

    public function testEmptyConfigGivesEmptyBoosts()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(array(), $config->getDefaultBoosts());
    }

    public function testEmptyConfigGivesEmptyFacets()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(array(), $config->getDefaultFacetingAttributes());
    }

    public function testEmptyConfigGivesEmptyIntercepts()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(array(), $config->getIntercepts());
    }

    public function testEmptyConfigGivesEmptyFilterAttributesList()
    {
        $config = new ContextConfiguration();
        $this->assertEquals(array(), $config->getFilterableAttributes());
    }

    public function testEmptyConfigMeansShouldNotIgnoreCurrentFilteredAttributesInFaceting()
    {
        $config = new ContextConfiguration();
        $this->assertFalse($config->shouldIgnoreCurrentFilteredAttributesInFaceting());
    }

    public function testFullConfigGivesItemsPerPage()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertEquals(12, $config->getDefaultItemsPerPage());
    }

    public function testFullConfigGivesDefaultFilterQueries()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertEquals(array('active' => true, 'in_stock' => true), $config->getDefaultFilterQueries());
    }

    public function testFullConfigGivesSortsForSearchTermsFromFallback()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertEquals(
            array('name' => ContextConfigurationInterface::ORDER_ASC, 'price' => ContextConfigurationInterface::ORDER_DESC),
            $config->getDefaultSortsForSearchTermQuery()
        );
    }

    public function testFullConfigGivesSortsForNonSearchTerm()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertEquals(
            array('velocity' => ContextConfigurationInterface::ORDER_DESC),
            $config->getDefaultSortsForNonSearchTermQuery()
        );
    }

    public function testFullConfigGivesBoosts()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertEquals(array('name' => 5, 'category' => 0.4), $config->getDefaultBoosts());
    }

    public function testFullConfigGivesFacets()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertEquals(array('gender', 'category', 'price'), $config->getDefaultFacetingAttributes());
    }

    public function testFullConfigGivesIntercepts()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertEquals(
            array(
                'sale' => array(
                    'terms' => array('sale'),
                    'type' => 'route',
                    'route' => 'shop_sale',
                    'route_params' => array(),
                ),
                '3xl' => array(
                    'terms' => array('XXXL', '3XL'),
                    'type' => 'search',
                    'filters' => array(
                        'size' => 'XXXL',
                    ),
                ),
            ),
            $config->getIntercepts()
        );
    }

    public function testFullConfigGivesFilterableAttributes()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertEquals(array('gender', 'color', 'size', 'on_sale'), $config->getFilterableAttributes());
    }

    public function testFullConfigGivesShouldIgnoreCurrentFiltersInFaceting()
    {
        $config = new ContextConfiguration($this->getFullConfiguration());
        $this->assertTrue($config->shouldIgnoreCurrentFilteredAttributesInFaceting());
    }

    /**
     * @return
     */
    private function getFullConfiguration()
    {
        return array(
            'items_per_page' => 12,
            'base_filter_queries' => array('active' => true, 'in_stock' => true),
            'sorts' => array('name' => ContextConfigurationInterface::ORDER_ASC, 'price' => ContextConfigurationInterface::ORDER_DESC),
            'sorts_non_search_term' => array('velocity' => ContextConfigurationInterface::ORDER_DESC),
            'boosts' => array('name' => 5, 'category' => 0.4),
            'facets' => array('gender', 'category', 'price'),
            'intercepts' => array(
                'sale' => array(
                    'terms' => array('sale'),
                    'type' => 'route',
                    'route' => 'shop_sale',
                    'route_params' => array(),
                ),
                '3xl' => array(
                    'terms' => array('XXXL', '3XL'),
                    'type' => 'search',
                    'filters' => array(
                        'size' => 'XXXL',
                    ),
                ),
            ),
            'filters' => array('gender', 'color', 'size', 'on_sale'),
            'should_ignore_current_filters_in_faceting' => true,
        );
    }
}
