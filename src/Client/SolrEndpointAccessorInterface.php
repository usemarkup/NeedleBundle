<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Client;

use Solarium\Core\Client\Endpoint;

/**
 * An interface for an accessor object for Solr endpoints.
 */
interface SolrEndpointAccessorInterface
{
    public function getEndpointForKey(?string $endpointKey): Endpoint;
}
