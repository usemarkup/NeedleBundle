<?php

namespace Markup\NeedleBundle\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
 * An interface for a provider object that can fetch information about available filter values.
 **/
interface AvailableFilterValueProviderInterface
{
    /**
     * Gets the filters that are available in a particular context.
     *
     * @return AttributeInterface[]
     **/
    public function getAvailableFilters();

    /**
     * Gets the filter values that are available for the given filter.
     *
     * @param  AttributeInterface                                 $filter
     * @return \Markup\NeedleBundle\Filter\FilterValueInterface[]
     **/
    public function getAvailableValuesForFilter(AttributeInterface $filter);
}
