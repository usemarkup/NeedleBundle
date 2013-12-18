<?php

namespace Markup\NeedleBundle\Filter;

/**
 * An interface for a URL manipulator that takes a URL and is able to manipulate it so it represents itself plus an applied filter query.
 **/
interface FilterQueryUrlManipulatorInterface
{
    /**
     * Manipulates the provided URL to return the same resource but with the provided filter query applied.
     *
     * @param  string               $url
     * @param  FilterQueryInterface $filterQuery
     * @return string
     **/
    public function manipulateUrlWithFilterQuery($url, FilterQueryInterface $filterQuery);

    /**
     * Strips filters from the provided URL - i.e. the URL passed provides a search corpus view, and this manipulation returns the URL that represents the same search corpus view but with filters removed.
     *
     * @param  string $url
     * @return string
     **/
    public function stripFiltersFromUrl($url);

    /**
     * Checks whether URL represents a view of a search corpus that has filters applied to it. Returns true if it does.
     *
     * @return bool
     **/
    public function checkUrlHasFilters($url);

    /**
     * Checks whether URL represents a view of a search corpus that has the provided filter query applied to it.
     *
     * @param  string               $url
     * @param  FilterQueryInterface $filterQuery
     * @return bool
     **/
    public function checkUrlHasFilterQuery($url, FilterQueryInterface $filterQuery);
}
