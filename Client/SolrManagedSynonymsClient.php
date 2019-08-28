<?php

namespace Markup\NeedleBundle\Client;

class SolrManagedSynonymsClient extends AbstractSolrManagedResourcesClient
{
    const RESOURCE_PATH = 'schema/analysis/synonyms/';

    /**
     * Returns all synonym mappings stored for a given resource as an array
     *
     * @param string $endpointKey
     * @param string $resourceId
     *
     * @return array
     */
    public function fetchData($resourceId, $endpointKey = null)
    {
        $responseContent = $this->fetch($resourceId, $endpointKey)->getContent();
        if (!$responseContent) {
            return [];
        }
        $mappings = json_decode($responseContent, true);

        return isset($mappings['synonymMappings']['managedMap']) ?
            $mappings['synonymMappings']['managedMap'] :
            [];
    }

    /**
     * @param string $resourceId
     * @param string $term
     * @param string $endpointKey
     *
     * @return bool
     */
    public function exists($resourceId, $term, $endpointKey = null)
    {
        $synonyms = $this->fetchData($resourceId, $endpointKey);

        return array_key_exists($term, $synonyms);
    }

    /**
     * @return string
     */
    public function getResourcePath()
    {
        return self::RESOURCE_PATH;
    }
}
