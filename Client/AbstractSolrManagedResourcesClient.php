<?php

namespace Markup\NeedleBundle\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractSolrManagedResourcesClient
{
    const METADATA_PATH = 'schema/managed';

    /**
     * @var SolrEndpointAccessorInterface
     */
    private $endpointAccessor;

    /**
     * @var SolrCoreAdminClient
     */
    private $solrCoreAdminClient;

    /**
     * @var Client
     */
    private $client;

    public function __construct(
        SolrEndpointAccessorInterface $endpointAccessor,
        LoggerInterface $logger = null
    ) {
        $this->endpointAccessor = $endpointAccessor;
        $this->client = new Client();
        $this->solrCoreAdminClient = new SolrCoreAdminClient(
            $endpointAccessor,
            $logger,
            $this->client
        );
    }

    /**
     * @return string
     */
    abstract public function getResourcePath();

    /**
     * @param string $resourceId
     * @param string $term
     * @param string $endpointKey
     *
     * @return bool
     */
    abstract public function exists($resourceId, $term, $endpointKey = null);

    /**
     * @param string $resourceId
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
            return new Response($e->getMessage(), $this->getStatusCodeForClientException($e));
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
            return new Response($e->getMessage(), $this->getStatusCodeForClientException($e));
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
            return new Response($e->getMessage(), $this->getStatusCodeForClientException($e));
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
            return new Response($e->getMessage(), $this->getStatusCodeForClientException($e));
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
        $endpoint = $this->endpointAccessor->getEndpointForKey($endpointKey);

        return $endpoint->getBaseUri();
    }

    private function getStatusCodeForClientException(ClientException $exception): int
    {
        return (!is_null($exception->getResponse())) ? $exception->getResponse()->getStatusCode() : 500;
    }
}
