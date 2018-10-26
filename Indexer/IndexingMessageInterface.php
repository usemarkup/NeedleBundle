<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Filter\FilterQueryInterface;

interface IndexingMessageInterface
{
    public function getSubjectIteration(): \Iterator;

    public function getCorpus(): string;

    public function isFullReindex(): bool;

    public function getPreDeleteQuery(): ?FilterQueryInterface;
}
