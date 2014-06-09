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
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->solariumResult->getIterator();
    }

    /**
     * @return ResultGroupInterface[]
     */
    public function getGroups()
    {
        $groups = array();
        foreach ($this->solariumResult->getResults() as $keyword => $term) {
            $groups[] = new SolrResultGroup($keyword, $term);
        }

        return $groups;
    }
}
