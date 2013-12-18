<?php

namespace Markup\NeedleBundle\Provider;

use Markup\NeedleBundle\Filter\FilterInterface;

/**
 * An interface for a provider object that can fetch information about available filter values.
 **/
interface AvailableFilterValueProviderInterface
{
    /**
     * Gets the filters that are available in a particular context.
     *
     * @return FilterInterface[]
     **/
    public function getAvailableFilters();

    /**
     * Gets the filter values that are available for the given filter.
     *
     * @param  FilterInterface                                         $filter
     * @return \Markup\NeedleBundle\Filter\FilterValueInterface[]
     **/
    public function getAvailableValuesForFilter(FilterInterface $filter);
}
