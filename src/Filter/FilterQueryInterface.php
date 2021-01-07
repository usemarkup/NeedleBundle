<?php

namespace Markup\NeedleBundle\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
 * An interface for a filter query, which is a combination of a filter object and a value for that filter.
 **/
interface FilterQueryInterface
{
    /**
     * Gets the key to use for a filter in filtered query on a search service.
     *
     * @return string
     **/
    public function getSearchKey();

    /**
     * Gets the string representation of a filter value in a filtered query on a search service.
     *
     * @return string
     **/
    public function getSearchValue();

    /**
     * Gets the filter object associated with this query.
     *
     * @return AttributeInterface
     **/
    public function getFilter();

    /**
     * Gets the filter value object associated with this query.
     *
     * @return FilterValueInterface
     **/
    public function getFilterValue();

    /**
     * Gets the type of the value contained within (one of the FilterValueInterface::TYPE_* constants).
     */
    public function getValueType(): string;
}
