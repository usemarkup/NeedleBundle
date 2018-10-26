<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

class IndexingResult implements IndexingResultInterface
{
    /**
     * @var bool
     */
    private $isSuccessful;

    /**
     * @var int|null
     */
    private $statusCode;

    /**
     * @var int
     */
    private $queryTimeInMilliseconds;

    /**
     * @var string
     */
    private $backendSoftware;

    public function __construct(
        bool $isSuccessful,
        ?int $statusCode,
        int $queryTimeInMilliseconds,
        string $backendSoftware
    ) {
        $this->isSuccessful = $isSuccessful;
        $this->statusCode = $statusCode;
        $this->queryTimeInMilliseconds = $queryTimeInMilliseconds;
        $this->backendSoftware = $backendSoftware;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * Gets whichever status code it is that the backend emits.
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getQueryTimeInMilliseconds(): int
    {
        return $this->queryTimeInMilliseconds;
    }

    public function getBackendSoftware(): string
    {
        return $this->backendSoftware;
    }
}
