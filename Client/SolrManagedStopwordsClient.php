<?php

namespace Markup\NeedleBundle\Client;

/**
 * Class SolrManagedResourcesClient
 */
class SolrManagedStopwordsClient extends AbstractSolrManagedResourcesClient
{
    const RESOURCE_PATH = 'schema/analysis/stopwords/';

    /**
     * Returns all stop words stored for a given resource as an array
     *
     * @param string $endpointKey
     * @param string $resourceId
     *
     * @return array
     */
    public function fetchData($resourceId, $endpointKey = null)
    {
        $response = $this->fetch($resourceId, $endpointKey);
        $mappings = json_decode($response->getContent(), true);

        return isset($mappings['wordSet']['managedList']) ?
            $mappings['wordSet']['managedList'] :
            [];
    }

    /**
     * @param string $resourceId
     * @param string $word
     * @param string $endpointKey
     *
     * @return bool
     */
    public function exists($resourceId, $word, $endpointKey = null)
    {
        $stopwords = $this->fetchData($resourceId, $endpointKey);

        return in_array($word, $stopwords);
    }

    /**
     * @return string
     */
    public function getResourcePath()
    {
        return self::RESOURCE_PATH;
    }
}
