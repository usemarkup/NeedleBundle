<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;

/**
 * An interface for a search service result.
 **/
interface ResultInterface extends \Countable, \IteratorAggregate
{
    /*
     * The inherited count method (from \Countable) returns the total result count
     * as it is not usually useful to know explicitly how many documents are present in a paged result.
     */

    /**
     * Gets the total count of the result set, regardless of any paging.
     *
     * @return int
     **/
    public function getNbResults();

    /**
     * Gets the time of the search query in milliseconds.
     *
     * @return float
     **/
    public function getQueryTimeInMilliseconds();

    /**
     * Gets the total number of pages for this result.
     *
     * @return int
     **/
    public function getNbPages();

    /**
     * @return int
     **/
    public function getCurrentPage();

    /**
     * Gets whether there are a sufficient number of result documents to paginate this result
     * (i.e. the number is greater than the max number to show per page).
     *
     * @return bool
     **/
    public function haveToPaginate();

    /**
     * Gets whether there is a page previous to the page currently being shown.
     *
     * @return bool
     **/
    public function hasPreviousPage();

    /**
     * Gets the number of the previous page.
     *
     * @return int
     * @throws PageDoesNotExistException if there is no previous page
     **/
    public function getPreviousPage();

    /**
     * Gets whether there is a page after the page currently being shown.
     *
     * @return bool
     **/
    public function hasNextPage();

    /**
     * Gets the number of the next page.
     *
     * @return int
     * @throws PageDoesNotExistException if there is no next page
     **/
    public function getNextPage();

    /**
     * Gets the facet sets that are returned with this result.
     *
     * @return \Markup\NeedleBundle\Facet\FacetSetInterface[]
     **/
    public function getFacetSets();

    /**
     * Gets a spellcheck result, if there is one (otherwise returns null).
     *
     * @return SpellcheckResultInterface|null
     */
    public function getSpellcheckResult();

    /**
     * Gets whether there is debug output for this result that could be displayed.
     *
     * @return bool
     **/
    public function hasDebugOutput();

    /**
     * Gets any debug output that could be displayed for this result - likely to be in HTML format
     * but this interface does not specify.  Returns null if there is no info to output.
     *
     * @return string|null
     **/
    public function getDebugOutput();

    /**
     * A mapping hash can be used to read values against the result where the eventual key with the document is unknown
     * (e.g for specialized attributes)
     */
    public function getMappingHashForFields(): array;

    public function getMaxPerPage(): int;
}
