<?php

namespace Markup\NeedleBundle\Facet;

/**
 * An interface for a provider of a facet sort order for a search engine, given a particular facet to sort.
 **/
interface SortOrderProviderInterface
{
    const SORT_BY_INDEX = 'index';
    const SORT_BY_COUNT = 'count';

    /**
     * Gets the sort order to use for sorting facet values in a search engine, given a particular facet.
     *
     * @param FacetInterface $facet
     *
     * @return mixed
     **/
    public function getSortOrderForFacet(FacetInterface $facet);
}
