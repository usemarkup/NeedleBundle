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
     * @var array|SolariumResult
     */
    private $data;

    /**
     * @param array|SolariumResult $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        if ($this->data instanceof SolariumResult) {
            return $this->data->count();
        }

        return (array_key_exists('matches', $this->data)) ? intval($this->data['matches']) : 0;
    }

    /**
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        if (is_array($this->data)) {
            return new \ArrayIterator($this->data);
        }

        return $this->data->getIterator();
    }

    /**
     * @return ResultGroupInterface[]
     */
    public function getGroups()
    {
        $groups = [];
        if ($this->data instanceof SolariumResult) {
            foreach ($this->data->getResults() as $keyword => $term) {
                $groups[] = new SolrResultGroup($keyword, $term);
            }

            return $groups;
        }
        if (!array_key_exists('groups', $this->data) || !is_array($this->data['groups'])) {
            return [];
        }

        return array_map(function ($groupData) {
            return new SolrResultGroup($groupData['groupValue'], $groupData['doclist']);
        }, $this->data['groups']);
    }

    /**
     * @return string[]
     */
    public function getTermSuggestions()
    {
        $terms = [];
        foreach ($this->getGroups() as $group) {
            foreach ($group->getDocuments() as $document) {
                $terms = array_merge($document->getValues(), $terms);
            }
        }

        $terms = array_unique($terms);
        sort($terms);

        return $terms;
    }
}
