<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

/**
 * An interface for an object that can take a marshalled indexing request and run it on a search backend.
 */
interface IndexingMessagerInterface
{
    public function executeIndex(
        IndexingMessageInterface $message,
        ?callable $perSubjectCallback = null
    ): IndexingResultInterface;
}
