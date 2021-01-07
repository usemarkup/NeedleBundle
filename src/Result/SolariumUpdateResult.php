<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Indexer\IndexingResultInterface;
use Solarium\QueryType\Update\Result;

class SolariumUpdateResult implements IndexingResultInterface
{
    /**
     * @var Result
     */
    private $result;

    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Gets whether the update was successful.
     */
    public function isSuccessful(): bool
    {
        return intval($this->result->getStatus()) === 0;
    }

    /**
     * Gets whichever status code it is that the backend emits.
     */
    public function getStatusCode(): int
    {
        return $this->result->getStatus();
    }

    public function getQueryTimeInMilliseconds(): int
    {
        return $this->result->getQueryTime();
    }

    public function getBackendSoftware(): string
    {
        return 'solr';
    }
}
