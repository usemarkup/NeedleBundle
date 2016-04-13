<?php

namespace Markup\NeedleBundle\Client;

use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Solarium\Client as Solarium;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class SolrCoreAdminClient
{
    /**
     * @var Solarium
     */
    private $solarium;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Solarium        $solarium
     * @param LoggerInterface $logger
     */
    public function __construct(Solarium $solarium, LoggerInterface $logger)
    {
        $this->solarium = $solarium;
        $this->client = new Client();
        $this->logger = $logger;
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
            $response = $this->client->get($url);
        } catch (ClientException $e) {
            $this->logger->error(sprintf('Core admin operation failed using URL: %s', $url));

            return new Response($e->getMessage(), $e->getResponse()->getStatusCode());
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
        $endpoint = $this->solarium->getEndpoint($endpointKey);
        $urlParameters = http_build_query(
            array_merge(
                [
                    'action' => $action,
                    'core' => $endpoint->getCore(),
                ],
                $parameters
            )
        );

        return sprintf("%s../admin/cores?%s", $endpoint->getBaseUri(), $urlParameters);
    }
}