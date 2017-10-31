<?php

namespace Markup\NeedleBundle\Service;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Helper\Iterators\SearchHitIterator;
use Elasticsearch\Helper\Iterators\SearchResponseIterator;

class ElasticSearchService
{
    /**
     * Elastic client.
     *
     * @var ClientBuilder
     **/
    private $elastic;


    /**
     * SolrSearchService constructor.
     * @param ClientBuilder $elastic
     */
    public function __construct(
        ClientBuilder $elastic
    ) {
        $this->elastic = new ClientBuilder();
    }

    /**
     * @param array $params
     * @return array
     */
    public function indexParams(array $params): array
    {
        return $this->getClient()->index($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getDocument(array $params): array
    {
        return $this->getClient()->get($params);
    }

    /**
     * @param array $params
     * @return \Iterator
     */
    public function search(array $params): \Iterator
    {
        $resultIterator = $this->createHitIterator($params);
        $resultIterator->rewind();

        return $resultIterator;
    }

    /**
     * @param $params
     * @return array
     */
    public function delete(array $params): array
    {
        return $this->getClient()->delete($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function deleteIndex(array $params): array
    {
        return $this->getClient()->indices()->delete($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function putMapping(array $params): array
    {
        return $this->getClient()->indices()->putMapping($params);
    }

    /**
     * @return \Elasticsearch\Client
     */
    private function getClient(): Client
    {
        return $this->elastic::create()->build();
    }

    /**
     * @param array $params
     * @return SearchHitIterator
     */
    private function createHitIterator(array $params): SearchHitIterator
    {
        /*
         * Keep this to return _scroll_id in response
         * Possible ElasticSearch bug, 404 when no scroll param passed
         */
        $params['scroll'] = $params['scroll'] ?? '1m';

        $searchResponseIterator = new SearchResponseIterator($this->getClient(), $params);
        $searchHitIterator = new SearchHitIterator($searchResponseIterator);

        return $searchHitIterator;
    }
}
