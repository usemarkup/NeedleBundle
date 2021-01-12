<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Filter\FilterQueryInterface;

class IndexingMessage implements IndexingMessageInterface
{
    /**
     * @var \Iterator
     */
    private $subjectIteration;

    /**
     * @var string
     */
    private $corpus;

    /**
     * @var bool
     */
    private $isFullReindex;

    /**
     * @var FilterQueryInterface|null
     */
    private $preDeleteQuery;

    public function __construct(
        \Iterator $subjectIteration,
        string $corpus,
        bool $isFullReindex,
        ?FilterQueryInterface $preDeleteQuery = null
    ) {
        $this->subjectIteration = $subjectIteration;
        $this->corpus = $corpus;
        $this->isFullReindex = $isFullReindex;
        $this->preDeleteQuery = $preDeleteQuery;
    }

    public function getSubjectIteration(): \Iterator
    {
        return $this->subjectIteration;
    }

    public function getCorpus(): string
    {
        return $this->corpus;
    }

    public function isFullReindex(): bool
    {
        return $this->isFullReindex;
    }

    public function getPreDeleteQuery(): ?FilterQueryInterface
    {
        return $this->preDeleteQuery;
    }
}
