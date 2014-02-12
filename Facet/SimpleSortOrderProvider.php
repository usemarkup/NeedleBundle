<?php

namespace Markup\NeedleBundle\Facet;

/**
 * A simple provider of sort orders for named facets. Sort order can only be by index or by count.
 */
class SimpleSortOrderProvider implements SortOrderProviderInterface
{
    /**
     * @var bool
     */
    private $shouldDefaultToIndex;

    /**
     * @var array
     */
    private $exceptionFacetNames;

    /**
     * @param bool $shouldDefaultToIndex
     * @param array $exceptionFacetNames
     */
    public function __construct($shouldDefaultToIndex = true, array $exceptionFacetNames = array())
    {
        $this->shouldDefaultToIndex = $shouldDefaultToIndex;
        $this->exceptionFacetNames = $exceptionFacetNames;
    }

    /**
     * Gets the sort order to use for sorting facet values in a search engine, given a particular facet.
     *
     * @param FacetInterface $facet
     *
     * @return string
     **/
    public function getSortOrderForFacet(FacetInterface $facet)
    {
        $defaultCase = ($this->shouldDefaultToIndex) ? SortOrderProviderInterface::SORT_BY_INDEX : SortOrderProviderInterface::SORT_BY_COUNT;
        $exceptionCase = ($this->shouldDefaultToIndex) ? SortOrderProviderInterface::SORT_BY_COUNT : SortOrderProviderInterface::SORT_BY_INDEX;

        return (in_array($facet->getName(), $this->exceptionFacetNames)) ? $exceptionCase : $defaultCase;
    }
}
