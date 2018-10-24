<?php

namespace Markup\NeedleBundle\Client;

use Solarium\Core\Client\Endpoint;

/**
 * An endpoint accessor for a specific corpus.
 */
class SolrEndpointAccessor implements SolrEndpointAccessorInterface
{
    /**
     * @var string
     */
    private $corpus;

    /**
     * @var BackendClientServiceLocator
     */
    private $clientLocator;

    public function __construct(string $corpus, BackendClientServiceLocator $clientLocator)
    {
        $this->corpus = $corpus;
        $this->clientLocator = $clientLocator;
    }

    public function getEndpointForKey(?string $endpointKey): Endpoint
    {
        return $this->clientLocator->fetchClientForCorpus($this->corpus)->getEndpoint($endpointKey);
    }
}
