<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

class NoopIndexingMessager implements IndexingMessagerInterface
{
    public function executeIndex(
        IndexingMessageInterface $message,
        ?callable $perSubjectCallback = null
    ): IndexingResultInterface {
        return new NoopIndexingResult();
    }
}
