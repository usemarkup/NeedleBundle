<?php

namespace Markup\NeedleBundle\Event;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Result\UpdateResultInterface;

/**
 * An event that is fired after a corpus update has finished.
 */
class CorpusPostUpdateEvent extends CorpusUpdateEvent
{
    /**
     * @var UpdateResultInterface
     */
    private $result;

    /**
     * @param CorpusInterface       $corpus
     * @param bool                  $isFullUpdate
     * @param UpdateResultInterface $result
     */
    public function __construct(CorpusInterface $corpus, $isFullUpdate, UpdateResultInterface $result)
    {
        parent::__construct($corpus, $isFullUpdate);
        $this->result = $result;
    }

    /**
     * @return \Markup\NeedleBundle\Result\UpdateResultInterface
     */
    public function getResult()
    {
        return $this->result;
    }
}
