<?php

namespace Markup\NeedleBundle\Event;

use Markup\NeedleBundle\Corpus\CorpusInterface;

abstract class CorpusUpdateEvent extends CorpusEvent
{
    /**
     * Whether this is a full update (i.e. the entire corpus is being replaced/updated)
     *
     * @var bool
     */
    private $isFullUpdate;

    /**
     * @param CorpusInterface $corpus
     * @param bool            $isFullUpdate
     */
    public function __construct(CorpusInterface $corpus, $isFullUpdate)
    {
        parent::__construct($corpus);
        $this->isFullUpdate = $isFullUpdate;
    }

    /**
     * @return bool
     */
    public function isFullUpdate()
    {
        return $this->isFullUpdate;
    }
}
