<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Markup\NeedleBundle\Service\SearchServiceInterface;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;
use Pagerfanta\Pagerfanta;
use Traversable;

/**
 * Decorates an underlying result by, if there is at least one spellcheck suggestion, fetching a new result from the backend based on a query on the first suggestion.
 */
class SuggestionResultDecorator implements ResultInterface
{
    /**
     * @var ResultInterface
     */
    private $originalResult;

    /**
     * @var SimpleQueryInterface
     */
    private $query;

    /**
     * SearchServiceInterface
     */
    private $searchService;

    /**
     * @var ResultInterface
     */
    private $suggestionResult;

    /**
     * @var bool
     */
    private $resolved;

    /**
     * @param ResultInterface        $originalResult
     * @param SimpleQueryInterface   $query
     * @param SearchServiceInterface $searchService
     */
    public function __construct(ResultInterface $originalResult, SimpleQueryInterface $query, SearchServiceInterface $searchService)
    {
        $this->originalResult = $originalResult;
        $this->query = $query;
        $this->searchService = $searchService;
        $this->resolved = false;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->getSuggestionResult()->getIterator();
    }

    /**
     * Gets the total count of the result set, regardless of any paging.
     *
     * @return int
     **/
    public function getTotalCount()
    {
        //this should use original result
        return $this->originalResult->getTotalCount();
    }

    /**
     * Gets the time of the search query in milliseconds.
     *
     * @return float
     **/
    public function getQueryTimeInMilliseconds()
    {
        //this should use original result
        return $this->originalResult->getQueryTimeInMilliseconds();
    }

    /**
     * Gets the total number of pages for this result.
     *
     * @return int
     **/
    public function getTotalPageCount()
    {
        //there should only be one results page
        return 1;
    }

    /**
     * @return int
     **/
    public function getCurrentPageNumber()
    {
        //there should only be one results page
        return 1;
    }

    /**
     * Gets whether there are a sufficient number of result documents to paginate this result (i.e. the number is greater than the max number to show per page).
     *
     * @return bool
     **/
    public function isPaginated()
    {
        //there should only be one results page
        return false;
    }

    /**
     * Gets whether there is a page previous to the page currently being shown.
     *
     * @return bool
     **/
    public function hasPreviousPage()
    {
        //there should only be one results page
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
        //there should only be one results page
        throw new PageDoesNotExistException();
    }

    /**
     * Gets whether there is a page after the page currently being shown.
     *
     * @return bool
     **/
    public function hasNextPage()
    {
        //there should only be one results page
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
        //there should only be one results page
        throw new PageDoesNotExistException();
    }

    /**
     * Gets the facet sets that are returned with this result.
     *
     * @return \Markup\NeedleBundle\Facet\FacetSetInterface[]
     **/
    public function getFacetSets()
    {
        //use original result
        return $this->originalResult->getFacetSets();
    }

    /**
     * Gets a spellcheck result, if there is one (otherwise returns null).
     *
     * @return SpellcheckResultInterface|null
     */
    public function getSpellcheckResult()
    {
        //expose origin result
        return $this->originalResult->getSpellcheckResult();
    }

    /**
     * Gets whether there is debug output for this result that could be displayed.
     *
     * @return bool
     **/
    public function hasDebugOutput()
    {
        //use original result
        return $this->originalResult->hasDebugOutput();
    }

    /**
     * Gets any debug output that could be displayed for this result - likely to be in HTML format, but this interface does not specify.  Returns null if there is no info to output.
     *
     * @return string|null
     **/
    public function getDebugOutput()
    {
        //use original result
        return $this->originalResult->getDebugOutput();
    }

    /**
     * It's likely there's a pagerfanta object on the result, so expose if present.
     *
     * @return Pagerfanta|null
     */
    public function getPagerfanta()
    {
        if (!method_exists($this->originalResult, 'getPagerfanta')) {
            return null;
        }

        return $this->originalResult->getPagerfanta();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        //use original result
        return count($this->originalResult);
    }

    private function getSuggestionResult()
    {
        if (!$this->resolved) {
            if ($this->originalResult->getSpellcheckResult() && $this->originalResult->getTotalCount() === 0 && count($this->originalResult->getSpellcheckResult()->getSuggestions()) > 0) {
                $suggestionQuery = clone $this->query;
                $suggestions = $this->originalResult->getSpellcheckResult()->getSuggestions();
                $suggestion = $suggestions[0];
                if (method_exists($suggestionQuery, 'setSearchTerm')) {
                    $suggestionQuery->setSearchTerm($suggestion->getWord());
                    $this->suggestionResult = $this->searchService->executeQuery($suggestionQuery);
                }
            }
            $this->resolved = true;
        }

        return $this->suggestionResult ?: $this->originalResult;
    }
}
