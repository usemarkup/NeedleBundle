<?php

namespace Markup\NeedleBundle\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Solarium\Client as Solarium;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SolrManagedResourcesClient
 * @package Markup\NeedleBundle\Client
 */
abstract class AbstractSolrManagedResourcesClient
{
    const METADATA_PATH = 'schema/managed';

    /**
     * @var Solarium
     */
    private $solarium;

    /**
     * @var SolrCoreAdminClient
     */
    private $solrCoreAdminClient;

    /**
     * @param Solarium            $solarium
     * @param SolrCoreAdminClient $solrCoreAdminClient
     */
    public function __construct(Solarium $solarium, SolrCoreAdminClient $solrCoreAdminClient)
    {
        $this->solarium = $solarium;
        $this->solrCoreAdminClient = $solrCoreAdminClient;
        $this->client = new Client();
    }

    /**
     * @return string
     */
    abstract public function getResourcePath();

    /**
     * @param        $resourceId
     * @param        $term
     * @param string $endpointKey
     *
     * @return bool
     */
    abstract public function exists($resourceId, $term, $endpointKey = null);

    /**
     * @param        $resourceId
     * @param string $endpointKey
     *
     * @return array
     */
    abstract public function fetchData($resourceId, $endpointKey = null);

    /**
     * Returns the schema metadata for a Solr endpoint
     *
     * @param string $endpointKey
     *
     * @return Response
     */
    public function fetchManagedSchemaMetadata($endpointKey = null)
    {
        $url = sprintf('%s%s', $this->getBaseUri($endpointKey), self::METADATA_PATH);

        try {
            $response = $this->client->get($url);
        } catch (ClientException $e) {
            return new Response($e->getMessage(), $e->getResponse()->getStatusCode());
        }

        return new Response($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * Returns resourceIds (including path) for all managed resources
     *
     * @param string $endpointKey
     *
     * @return array
     */
    public function fetchAllFullyQualifiedResourceIds($endpointKey = null)
    {
        $metadataJson = $this->fetchManagedSchemaMetadata($endpointKey);
        $metadata = json_decode($metadataJson->getContent(), true);

        $resourceIds = [];
        if (isset($metadata['managedResources'])) {
            foreach ($metadata['managedResources'] as $resource) {
                $resourceIds[] = $resource['resourceId'];
            }
        }

        return $resourceIds;
    }

    /**
     * Returns an array of managed resource ids as defined in the Solr schema (without paths)
     *
     * @param string $endpointKey
     *
     * @return array
     */
    public function fetchResourceIds($endpointKey = null)
    {
        $metadataJson = $this->fetchManagedSchemaMetadata($endpointKey);
        $metadata = json_decode($metadataJson->getContent(), true);
        $path = sprintf('/%s', $this->getResourcePath());

        $resourceIds = [];
        if (isset($metadata['managedResources'])) {
            foreach ($metadata['managedResources'] as $resource) {
                if (strpos($resource['resourceId'], $path) === 0) {
                    $resourceIds[] = str_replace($path, '', $resource['resourceId']);
                }
            }
        }

        return $resourceIds;
    }

    /**
     * Adds elements to a managed resource
     *
     * @param string $resourceId
     * @param array  $data
     * @param string $endpointKey
     *
     * @return Response
     */
    public function add($resourceId, array $data, $endpointKey = null)
    {
        $data = array_change_key_case($data);
        $url = $this->getResourceUrl($resourceId, $endpointKey);

        try {
            $response = $this->client->put($url, ['body' => json_encode($data)]);
        } catch (ClientException $e) {
            return new Response($e->getMessage(), $e->getResponse()->getStatusCode());
        }

        $this->solrCoreAdminClient->reload($endpointKey);

        return new Response($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * Removes an element from a managed resource
     *
     * @param string $resourceId
     * @param string $term
     * @param string $endpointKey
     *
     * @return Response
     */
    public function delete($resourceId, $term, $endpointKey = null)
    {
        $term = mb_strtolower($term);
        $url = sprintf('%s/%s', $this->getResourceUrl($resourceId, $endpointKey), $term);

        try {
            $response = $this->client->delete($url);
        } catch (ClientException $e) {
            return new Response($e->getMessage(), $e->getResponse()->getStatusCode());
        }

        $this->solrCoreAdminClient->reload($endpointKey);

        return new Response($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * Returns the full JSON representation of a managed resource
     *
     * @param string $resourceId
     * @param string $endpointKey
     *
     * @return Response
     */
    public function fetch($resourceId, $endpointKey = null)
    {
        $url = $this->getResourceUrl($resourceId, $endpointKey);

        try {
            $response = $this->client->get($url);
        } catch (ClientException $e) {
            return new Response($e->getMessage(), $e->getResponse()->getStatusCode());
        }

        return new Response($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @param string $resourceId
     * @param string $endpointKey
     *
     * @return string
     */
    private function getResourceUrl($resourceId, $endpointKey = null)
    {
        return sprintf('%s%s%s', $this->getBaseUri($endpointKey), $this->getResourcePath(), $resourceId);
    }

    /**
     * @param string $endpointKey
     *
     * @return string
     */
    private function getBaseUri($endpointKey = null)
    {
        $endpoint = $this->solarium->getEndpoint($endpointKey);

        return $endpoint->getBaseUri();
    }
}