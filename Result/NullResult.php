<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;

/**
 * Null implementation of a search result.
 */
class NullResult implements ResultInterface
{
    public function getIterator()
    {
        return new \ArrayIterator();
    }

    public function count()
    {
        return 0;
    }

    /**
     * Gets the total count of the result set, regardless of any paging.
     *
     * @return int
     **/
    public function getTotalCount()
    {
        return 0;
    }

    /**
     * Gets the time of the search query in milliseconds.
     *
     * @return float
     **/
    public function getQueryTimeInMilliseconds()
    {
        return 0;
    }

    /**
     * Gets the total number of pages for this result.
     *
     * @return int
     **/
    public function getTotalPageCount()
    {
        return 1;
    }

    /**
     * @return int
     **/
    public function getCurrentPageNumber()
    {
        return 1;
    }

    /**
     * Gets whether there are a sufficient number of result documents to paginate this result (i.e. the number is greater than the max number to show per page).
     *
     * @return bool
     **/
    public function isPaginated()
    {
        return false;
    }

    /**
     * Gets whether there is a page previous to the page currently being shown.
     *
     * @return bool
     **/
    public function hasPreviousPage()
    {
        return false;
    }

    /**
     * Gets the number of the previous page.
     *
     * @return int
     * @throws PageDoesNotExistException if there is no previous page
     **/
    public function getPreviousPageNumber()
    {
        throw new PageDoesNotExistException();
    }

    /**
     * Gets whether there is a page after the page currently being shown.
     *
     * @return bool
     **/
    public function hasNextPage()
    {
        return false;
    }

    /**
     * Gets the number of the next page.
     *
     * @return int
     * @throws PageDoesNotExistException if there is no next page
     **/
    public function getNextPageNumber()
    {
        throw new PageDoesNotExistException();
    }

    /**
     * Gets the facet sets that are returned with this result.
     *
     * @return \Markup\NeedleBundle\Facet\FacetSetInterface[]|iterable
     **/
    public function getFacetSets()
    {
        return new \ArrayIterator();
    }

    /**
     * Gets a spellcheck result, if there is one (otherwise returns null).
     *
     * @return SpellcheckResultInterface|null
     */
    public function getSpellcheckResult()
    {
        return null;
    }

    /**
     * Gets whether there is debug output for this result that could be displayed.
     *
     * @return bool
     **/
    public function hasDebugOutput()
    {
        return false;
    }

    /**
     * Gets any debug output that could be displayed for this result - likely to be in HTML format, but this interface does not specify.  Returns null if there is no info to output.
     *
     * @return string|null
     **/
    public function getDebugOutput()
    {
        return null;
    }
}
