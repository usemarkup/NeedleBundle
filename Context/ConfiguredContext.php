<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Boost\BoostQueryField;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Filter\FilterQuery;
use Markup\NeedleBundle\Filter\ScalarFilterValue;
use Markup\NeedleBundle\Filter\UnionFilterValue;
use Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Sort\SortCollection;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * A context that uses a configuration.
 */
class ConfiguredContext implements SearchContextInterface
{
    /**
     * @var ContextConfigurationInterface
     */
    private $config;

    /**
     * @var AttributeProviderInterface
     */
    private $attributeProvider;

    /**
     * @var FacetProviderInterface
     */
    private $facetProvider;

    /**
     * @var FacetSetDecoratorProviderInterface
     */
    private $facetSetDecoratorProvider;

    /**
     * @var CollatorProviderInterface
     */
    private $facetCollatorProvider;

    /**
     * @var SortOrderProviderInterface
     */
    private $facetSortOrderProvider;

    /**
     * @var ConfiguredInterceptorProvider
     */
    private $interceptorProvider;

    /**
     * @param ContextConfigurationInterface      $config
     * @param AttributeProviderInterface         $attributeProvider
     * @param FacetProviderInterface             $facetProvider
     * @param FacetSetDecoratorProviderInterface $facetSetDecoratorProvider
     * @param CollatorProviderInterface          $facetCollatorProvider
     * @param SortOrderProviderInterface         $facetSortOrderProvider
     * @param ConfiguredInterceptorProvider      $interceptorProvider
     */
    public function __construct(
        ContextConfigurationInterface $config,
        AttributeProviderInterface $attributeProvider,
        FacetProviderInterface $facetProvider,
        FacetSetDecoratorProviderInterface $facetSetDecoratorProvider,
        CollatorProviderInterface $facetCollatorProvider,
        SortOrderProviderInterface $facetSortOrderProvider,
        ConfiguredInterceptorProvider $interceptorProvider
    ) {
        $this->config = $config;
        $this->attributeProvider = $attributeProvider;
        $this->facetProvider = $facetProvider;
        $this->facetSetDecoratorProvider = $facetSetDecoratorProvider;
        $this->facetCollatorProvider = $facetCollatorProvider;
        $this->facetSortOrderProvider = $facetSortOrderProvider;
        $this->interceptorProvider = $interceptorProvider;
    }

    /**
     * Gets the number of items that should be shown per page in a paged view.  Returns null if no constraint on numbers exists.
     *
     * @return int|null
     **/
    public function getItemsPerPage()
    {
        return $this->config->getDefaultItemsPerPage() ?: null;
    }

    /**
     * Gets the set of facets to apply to the search.
     *
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface[]
     **/
    public function getFacets()
    {
        $facets = [];
        foreach ($this->config->getDefaultFacetingAttributes() as $facetName) {
            $facets[] = $this->facetProvider->getFacetByName($facetName);
        }

        return $facets;
    }

    /**
     * Gets the default filters to be applied to any search with this context.
     *
     * @return \Markup\NeedleBundle\Filter\FilterQueryInterface[]
     **/
    public function getDefaultFilterQueries()
    {
        $queries = [];
        foreach ($this->config->getDefaultFilterQueries() as $attr => $value) {
            if (!is_array($value)) {
                $q = new FilterQuery($this->attributeProvider->getAttributeByName($attr), new ScalarFilterValue($value));
            } else {
                $q = new FilterQuery(
                    $this->attributeProvider->getAttributeByName($attr),
                    new UnionFilterValue(
                        array_map(function ($filterValue) {
                            return new ScalarFilterValue($filterValue);
                        }, $value)
                    )
                );
            }
            $queries[] = $q;
        }

        return $queries;
    }

    /**
     * Gets the default sort collection to be applied to a query using this context.
     *
     * @param SelectQueryInterface $query
     *
     * @return SortCollectionInterface
     **/
    public function getDefaultSortCollectionForQuery(SelectQueryInterface $query)
    {
        $sortConfig = ($query->shouldTreatAsTextSearch())
            ? $this->config->getDefaultSortsForSearchTermQuery()
            : $this->config->getDefaultSortsForNonSearchTermQuery();

        return $this->createSortCollectionForConfig($sortConfig);
    }

    /**
     * Creates a sort collection from a hash of attribute names and directions.
     *
     * @param  array                   $sortConfig
     * @return SortCollectionInterface
     */
    private function createSortCollectionForConfig(array $sortConfig)
    {
        $sorts = new SortCollection();
        $contextSortAttributeFactory = new ContextSortAttributeFactory($this->attributeProvider);

        foreach ($sortConfig as $attr => $direction) {
            //allow legacy format (using hash as if it is ordered - which it is in PHP but not in other languages like JavaScript)
            if (is_array($direction)) {
                foreach ($direction as $attrKey => $dir) {
                    $sorts->add($contextSortAttributeFactory->createSortForAttributeNameAndDirection($attrKey, $dir));
                }
                continue;
            }
            $sorts->add($contextSortAttributeFactory->createSortForAttributeNameAndDirection($attr, $direction));
        }

        return $sorts;
    }

    /**
     * Gets the facet set decorator to apply for a specific facet. (This can determine how a facet set renders.) Returns false if no decoration to be applied.
     *
     * @param  AttributeInterface                                         $facet
     * @return \Markup\NeedleBundle\Facet\FacetSetDecoratorInterface|bool
     **/
    public function getSetDecoratorForFacet(AttributeInterface $facet)
    {
        return $this->facetSetDecoratorProvider->getDecoratorForFacet($facet);
    }

    /**
     * Gets whether the given facet that is being displayed should ignore any corresponding filter values that are currently selected (true), or whether they should just reflect the returned results (false).
     *
     * @return bool
     **/
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet)
    {
        return $this->config->shouldIgnoreCurrentFilteredAttributesInFaceting();
    }

    /**
     * Gets a list of available filter names that a userland query using this context can filter on.
     *
     * @return array
     **/
    public function getAvailableFilterNames()
    {
        return $this->config->getFilterableAttributes();
    }

    /**
     * Gets the set of boost query fields that should be defined against this query.
     *
     * @return array
     **/
    public function getBoostQueryFields()
    {
        $fields = [];
        foreach ($this->config->getDefaultBoosts() as $attr => $factor) {
            $fields[] = new BoostQueryField($this->attributeProvider->getAttributeByName($attr), $factor);
        }

        return $fields;
    }

    /**
     * Gets a provider object for collator (sorter) objects that can collate facet values.  May return null if no userland sorting of values should be done.
     *
     * @return \Markup\NeedleBundle\Collator\CollatorProviderInterface
     **/
    public function getFacetCollatorProvider()
    {
        return $this->facetCollatorProvider;
    }

    /**
     * Gets a signifier for the sort order to use for sorting of facet values within the search engine itself (i.e. not within the web application).
     *
     * @return \Markup\NeedleBundle\Facet\SortOrderProviderInterface
     **/
    public function getFacetSortOrderProvider()
    {
        return $this->facetSortOrderProvider;
    }

    /**
     * Gets an interceptor object that can intercept a lookup on a backend and provide redirects to specific places.
     *
     * @return \Markup\NeedleBundle\Intercept\InterceptorInterface
     **/
    public function getInterceptor()
    {
        return $this->interceptorProvider->createInterceptor($this->config->getIntercepts());
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRequestFacetValueForMissing()
    {
        return false;
    }
}
