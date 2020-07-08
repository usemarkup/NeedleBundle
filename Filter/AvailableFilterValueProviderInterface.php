<?php

namespace Markup\NeedleBundle\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\SpecializationContextHashInterface;

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
    public function getAvailableFilters(SpecializationContextHashInterface $contextHash);

    /**
     * Gets the filter values that are available for the given filter.
     *
     * @return array
     **/
    public function getAvailableValuesForFilter(
        AttributeInterface $filter,
        SpecializationContextHashInterface $contextHash
    );
}
