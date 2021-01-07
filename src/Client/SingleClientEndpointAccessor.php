<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Client;

use Solarium\Client as Solarium;
use Solarium\Core\Client\Endpoint;

/**
 * An endpoint accessor using a specific Solarium client object.
 */
class SingleClientEndpointAccessor implements SolrEndpointAccessorInterface
{
    /**
     * @var Solarium
     */
    private $solariumClient;

    public function __construct(Solarium $solariumClient)
    {
        $this->solariumClient = $solariumClient;
    }

    public function getEndpointForKey(?string $endpointKey): Endpoint
    {
        return $this->solariumClient->getEndpoint($endpointKey);
    }
}
