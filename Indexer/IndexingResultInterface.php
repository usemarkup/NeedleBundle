<?php

namespace Markup\NeedleBundle\Indexer;

/**
 * Interface for a result from an operation to perform indexing on a search backend.
 */
interface IndexingResultInterface
{
    public function isSuccessful(): bool;

    /**
     * Gets whichever status code it is that the backend emits.
     */
    public function getStatusCode(): ?int;

    public function getQueryTimeInMilliseconds(): int;

    public function getBackendSoftware(): string;
}
