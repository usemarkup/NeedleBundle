<?php

namespace Markup\NeedleBundle\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Solarium\Client as SolariumClient;
use Symfony\Component\HttpFoundation\Response;

class SolrCoreAdminClient
{
    /**
     * @var SolariumClient
     */
    private $solariumClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * SolrCoreAdminClient constructor.
     *
     * @param SolariumClient $solariumClient
     * @param LoggerInterface|null $logger
     * @param GuzzleClient|null $guzzleClient
     */
    public function __construct(
        SolariumClient $solariumClient,
        LoggerInterface $logger = null,
        GuzzleClient $guzzleClient = null
    ) {
        $this->solariumClient = $solariumClient;
        $this->logger = $logger ?: new NullLogger();
        $this->guzzleClient = $guzzleClient ?: new GuzzleClient();
    }

    /**
     * @param string $endpointKey
     *
     * @return Response
     */
    public function reload($endpointKey = null)
    {
        return $this->executeAction('RELOAD', [], $endpointKey);
    }

    /**
     * @param string $action
     * @param array  $parameters
     * @param string $endpointKey
     *
     * @return Response
     */
    private function executeAction($action, $parameters = [], $endpointKey = null)
    {
        $url = $this->getUriForAction($action, $parameters, $endpointKey);

        try {
            $response = $this->guzzleClient->get($url);
        } catch (ClientException $e) {
            $this->logger->error(sprintf('Core admin operation failed using URL: %s', $url));

            return new Response(
                $e->getMessage(),
                (!is_null($e->getResponse())) ? $e->getResponse()->getStatusCode() : 500
            );
        }

        return new Response($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @param string $action
     * @param array  $parameters
     * @param string $endpointKey
     *
     * @return string
     */
    private function getUriForAction($action, $parameters = [], $endpointKey = null)
    {
        $endpoint = $this->solariumClient->getEndpoint($endpointKey);
        $urlParameters = http_build_query(
            array_merge(
                [
                    'action' => $action,
                    'core'   => $endpoint->getCore(),
                ],
                $parameters
            )
        );

        return sprintf("%s../admin/cores?%s", $endpoint->getBaseUri(), $urlParameters);
    }
}
