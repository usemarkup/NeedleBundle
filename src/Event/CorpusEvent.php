<?php

namespace Markup\NeedleBundle\Event;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class CorpusEvent extends Event
{
    /**
     * @var CorpusInterface
     */
    private $corpus;

    /**
     * @param CorpusInterface $corpus
     */
    public function __construct(CorpusInterface $corpus)
    {
        $this->corpus = $corpus;
    }

    /**
     * @return \Markup\NeedleBundle\Corpus\CorpusInterface
     */
    public function getCorpus()
    {
        return $this->corpus;
    }
}
