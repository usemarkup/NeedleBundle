<?php

namespace Markup\NeedleBundle\Filter;

/**
 * An interface for a value for a filter.
 **/
interface FilterValueInterface
{
    const TYPE_SIMPLE = 'simple';
    const TYPE_INTERSECTION = 'intersection';
    const TYPE_UNION = 'union';
    const TYPE_RANGE = 'range';

    /**
     * Gets the representation of a filter value that should be used on a search service.
     *
     * @return mixed
     **/
    public function getSearchValue();

    /**
     * Gets the slug form of this filter value (i.e. the string form that would be used as part of a URL).
     *
     * @return string
     **/
    public function getSlug();

    /**
     * Gets the type for this value. Must be one of the FilterValueInterface::TYPE_* constants.
     */
    public function getValueType(): string;
}
