<?php

namespace Markup\NeedleBundle\Adapter;

use Markup\NeedleBundle\Facet\FacetSet;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\DocumentInterface;

/**
 * Wraps a very specific solrarium result format associated with the GroupingComponent in
 * a way that makes it easy for the GroupedQuerySolariumAdapter to use.
 *
 * Implements the Solarium Result interface representing itself as the result of a 'Select' query
 *
 * Will use the first document in each group as the 'document' and then embed _within_ that document
 * a property called 'groups' which will contain the whole group of results (including the initial document itself).
 * This provides a result that can be treated in the same way as a non grouped result.
 * Assumes there is only one 'grouping' of documents
 */
class GroupedResultAdapter implements ResultInterface, \IteratorAggregate, \Countable
{
    /**
     * @vay ResultInterface
     */
    private $result;

    /**
     * @param ResultInterface $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Splits the document out to give the desired format whereby the primary document
     * has other documents embeded within a 'groups' property
     * @return Document[]
     */
    private function parseResult()
    {
        $group = $this->getSingleGroup();
        $result = [];
        foreach ($group->getValueGroups() as $valueGroup) {
            $documents = $valueGroup->getDocuments();
            $primary = reset($documents);
            $fields = iterator_to_array($primary);
            $fields['groups'] = $documents;
            $result[] = new Document($fields);
        }

        return $result;
    }

    /**
     *
     * Returns the total number of 'groups' that this query will return
     * (not the number of distinct documents)
     *
     * @return int
     */
    public function getNumFound()
    {
        $group = $this->getSingleGroup();

        return $group->getNumberOfGroups();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->result->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->result->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->result->getData();
    }

    /**
     * Get Solr status code
     *
     * This is not the HTTP status code! The normal value for success is 0.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->result->getStatus();
    }

    /**
     * Get Solr query time
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @return int
     */
    public function getQueryTime()
    {
        return $this->result->getQueryTime();
    }

    /**
     * get Solr maxscore
     *
     * Returns the highest score of the documents in the total result for your current query (ignoring paging)
     * Will only be available if 'score' was one of the requested fields in your query
     *
     * @return float
     */
    public function getMaxScore()
    {
        return $this->result->getMaxScore();
    }

    /**
     * Get all component results
     *
     * @return array
     */
    public function getComponents()
    {
        return $this->result->getComponents();
    }

    /**
     * Get a component result by key
     *
     * @param  string $key
     * @return mixed
     */
    public function getComponent($key)
    {
        return $this->result->getComponents($key);
    }

    /**
     * Get morelikethis component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\MoreLikeThis\Result
     */
    public function getMoreLikeThis()
    {
        return $this->result->getMoreLikeThis();
    }

    /**
     * Get highlighting component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Highlighting\Result
     */
    public function getHighlighting()
    {
        return $this->result->getHighlighting();
    }

    /**
     * Get grouping component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Grouping\Result
     */
    public function getGrouping()
    {
        return $this->result->getGrouping();
    }

    /**
     * Get facetset component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return FacetSet
     */
    public function getFacetSet()
    {
        return $this->result->getFacetSet();
    }

    /**
     * Get spellcheck component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Spellcheck\Result
     */
    public function getSpellcheck()
    {
        return $this->result->getSpellcheck();
    }

    /**
     * Get stats component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Stats\Result
     */
    public function getStats()
    {
        return $this->result->getStats();
    }

    /**
     * Get debug component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Debug\Result
     */
    public function getDebug()
    {
        return $this->result->getDebug();
    }

    /**
     * Get all documents - documents are manipulated via 'parseResult' methods before being returned
     *
     * @return DocumentInterface[]
     */
    public function getDocuments()
    {
        return $this->parseResult();
    }

    /**
     * IteratorAggregate implementation
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parseResult());
    }

    /**
     * Countable implementation
     *
     * @return int
     */
    public function count()
    {
        return count($this->parseResult());
    }

    /**
     * Extracts one 'group' from the result. As per the constructor, this method relies on the
     * single grouping being accessible.
     */
    private function getSingleGroup()
    {
        if (!$this->result) {
            throw new \Exception('This adapter must have a result set on it before being accessed');
        }
        $grouping = $this->result->getGrouping();
        $groups = reset($grouping);
        $group = reset($groups);

        return $group;
    }
}
