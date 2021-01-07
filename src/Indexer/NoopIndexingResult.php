<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

class NoopIndexingResult implements IndexingResultInterface
{
    public function isSuccessful(): bool
    {
        return false;
    }

    /**
     * Gets whichever status code it is that the backend emits.
     */
    public function getStatusCode(): ?int
    {
        return null;
    }

    public function getQueryTimeInMilliseconds(): int
    {
        return 0;
    }

    public function getBackendSoftware(): string
    {
        return '/dev/null';
    }
}
