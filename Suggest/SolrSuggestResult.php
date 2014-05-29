<?php

namespace Markup\NeedleBundle\Suggest;

use Solarium\QueryType\Suggester\Result\Result as SolariumResult;
use Traversable;

/**
 * A suggest result wrapping a result from Solr/Solarium.
 */
class SolrSuggestResult implements \IteratorAggregate, SuggestResultInterface
{
    /**
     * @var SolariumResult
     */
    private $solariumResult;

    /**
     * @param SolariumResult $solariumResult
     */
    public function __construct(SolariumResult $solariumResult)
    {
        $this->solariumResult = $solariumResult;
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return $this->solariumResult->count();
    }

    /**
     * @return string[]
     */
    public function getSuggestions()
    {
        $suggestions = array();
        foreach ($this->solariumResult->getResults() as $termResult) {
            foreach ($termResult as $result) {
                $suggestions[] = $result;
            }
        }

        return $suggestions;
    }

    /**
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->solariumResult->getIterator();
    }
}
