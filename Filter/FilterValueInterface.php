<?php

namespace Markup\NeedleBundle\Filter;

/**
 * An interface for a value for a filter.
 **/
interface FilterValueInterface
{
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
}
