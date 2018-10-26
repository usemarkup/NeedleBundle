<?php

namespace Markup\NeedleBundle\Event;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Indexer\IndexingResultInterface;
use Markup\NeedleBundle\Result\UpdateResultInterface;

/**
 * An event that is fired after a corpus update has finished.
 */
class CorpusPostUpdateEvent extends CorpusUpdateEvent
{
    /**
     * @var IndexingResultInterface
     */
    private $result;

    public function __construct(CorpusInterface $corpus, bool $isFullUpdate, IndexingResultInterface $result)
    {
        parent::__construct($corpus, $isFullUpdate);
        $this->result = $result;
    }

    public function getResult(): IndexingResultInterface
    {
        return $this->result;
    }
}
