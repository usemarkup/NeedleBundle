<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Markup\NeedleBundle\Service\SearchServiceInterface;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;
use Pagerfanta\Pagerfanta;

/**
 * Decorates an underlying result by, if there is at least one spellcheck suggestion, fetching a new result from the backend based on a query on the first suggestion.
 */
class SuggestionResultDecorator implements ResultInterface, CanExposePagerfantaInterface
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
     * @var bool
     */
    private $resolved;

    /**
     * @var bool
     */
    private $useOriginalFacets;

    /**
     * @var ResultInterface|null
     */
    private $suggestionResult;

    /**
     * @var array
     */
    private $mappingHashForFields;

    public function __construct(
        ResultInterface $originalResult,
        SimpleQueryInterface $query,
        SearchServiceInterface $searchService,
        bool $useOriginalFacets = true
    ) {
        $this->originalResult = $originalResult;
        $this->query = $query;
        $this->searchService = $searchService;
        $this->useOriginalFacets = $useOriginalFacets;
        $this->resolved = false;
        $this->mappingHashForFields = [];
    }

    public function getIterator()
    {
        return $this->getSuggestionResult()->getIterator();
    }

    /**
     * Gets the total count of the result set, regardless of any paging.
     *
     * @return int
     **/
    public function getNbResults()
    {
        //this should use original result
        return $this->originalResult->getNbResults();
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
    public function getNbPages()
    {
        //there should only be one results page
        return 1;
    }

    /**
     * @return int
     **/
    public function getCurrentPage()
    {
        //there should only be one results page
        return 1;
    }

    /**
     * Gets whether there are a sufficient number of result documents to paginate this result (i.e. the number is greater than the max number to show per page).
     *
     * @return bool
     **/
    public function haveToPaginate()
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
    public function getPreviousPage()
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
    public function getNextPage()
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
        $result = ($this->useOriginalFacets) ? $this->originalResult : $this->getSuggestionResult();

        return $result->getFacetSets();
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
     */
    public function getPagerfanta(): ?Pagerfanta
    {
        if (!$this->originalResult instanceof CanExposePagerfantaInterface) {
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
            $this->resolveIfPossible();
            $this->resolved = true;
        }

        return $this->suggestionResult ?: $this->originalResult;
    }

    /**
     * TODO: some of this code doesn't make a lot of sense
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function resolveIfPossible()
    {
        $spellcheckResult = $this->originalResult->getSpellcheckResult();
        if (!$spellcheckResult) {
            return;
        }
        if ($this->originalResult->getNbResults() !== 0 || count($spellcheckResult->getSuggestions()) === 0) {
            return;
        }
        $suggestionQuery = clone $this->query;
        $suggestions = $spellcheckResult->getSuggestions();
        $suggestion = $suggestions[0];
    }

    public function setMappingHashForFields(array $hash)
    {
        $this->mappingHashForFields = $hash;
    }

    public function getMappingHashForFields(): array
    {
        return $this->mappingHashForFields;
    }

    public function getMaxPerPage(): int
    {
        return $this->originalResult->getMaxPerPage();
    }
}
