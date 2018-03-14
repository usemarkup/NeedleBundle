<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
 * A trait providing template (i.e. following template pattern) methods/ instance variable for an implementation of a decorated context.
 */
trait DecorateContextTrait
{
    /**
     * @var SearchContextInterface
     */
    private $context;

    /**
     * Gets the number of items that should be shown per page in a paged view.  Returns null if no constraint on numbers exists.
     *
     * @return int|null
     **/
    public function getItemsPerPage()
    {
        return $this->context->getItemsPerPage();
    }

    /**
     * Gets the set of facets to apply to the search.
     *
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface[]
     **/
    public function getFacets()
    {
        return $this->context->getFacets();
    }

    /**
     * Gets the default filters to be applied to any search with this context.
     *
     * @return \Markup\NeedleBundle\Filter\FilterQueryInterface[]
     **/
    public function getDefaultFilterQueries()
    {
        return $this->context->getDefaultFilterQueries();
    }

    /**
     * Gets the default sort collection to be applied to a query using this context.
     *
     * @param SelectQueryInterface $query
     *
     * @return \Markup\NeedleBundle\Sort\SortCollectionInterface
     **/
    public function getDefaultSortCollectionForQuery(SelectQueryInterface $query)
    {
        return $this->context->getDefaultSortCollectionForQuery($query);
    }

    /**
     * Gets the facet set decorator to apply for a specific facet. (This can determine how a facet set renders.) Returns null if no decoration to be applied.
     *
     * @param  AttributeInterface                                         $facet
     * @return \Markup\NeedleBundle\Facet\FacetSetDecoratorInterface|null
     **/
    public function getSetDecoratorForFacet(AttributeInterface $facet)
    {
        return $this->context->getSetDecoratorForFacet($facet);
    }

    /**
     * Gets whether the given facet that is being displayed should ignore any corresponding filter values that are currently selected (true), or whether they should just reflect the returned results (false).
     *
     * @param  AttributeInterface $facet
     * @return bool
     **/
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet)
    {
        return $this->context->getWhetherFacetIgnoresCurrentFilters($facet);
    }

    /**
     * Gets a list of available filter names that a userland query using this context can filter on.
     *
     * @return array
     **/
    public function getAvailableFilterNames()
    {
        return $this->context->getAvailableFilterNames();
    }

    /**
     * Gets the set of boost query fields that should be defined against this query.
     *
     * @return array
     **/
    public function getBoostQueryFields()
    {
        return $this->context->getBoostQueryFields();
    }

    /**
     * Gets a provider object for collator (sorter) objects that can collate facet values.  May return null if no userland sorting of values should be done.
     *
     * @return \Markup\NeedleBundle\Collator\CollatorProviderInterface
     **/
    public function getFacetCollatorProvider()
    {
        return $this->context->getFacetCollatorProvider();
    }

    /**
     * Gets a signifier for the sort order to use for sorting of facet values within the search engine itself (i.e. not within the web application).
     *
     * @return \Markup\NeedleBundle\Facet\SortOrderProviderInterface
     **/
    public function getFacetSortOrderProvider()
    {
        return $this->context->getFacetSortOrderProvider();
    }

    /**
     * Gets an interceptor object that can intercept a lookup on a backend and provide redirects to specific places.
     *
     * @return \Markup\NeedleBundle\Intercept\InterceptorInterface
     **/
    public function getInterceptor()
    {
        return $this->context->getInterceptor();
    }

    /**
     * Gets whether a query should request facet values for missing (i.e. no match on that facet)
     *
     * @return boolean
     **/
    public function shouldRequestFacetValueForMissing()
    {
        return $this->context->shouldRequestFacetValueForMissing();
    }
}
