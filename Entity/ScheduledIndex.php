<?php

namespace Markup\NeedleBundle\Entity;

class ScheduledIndex
{
    const SCHEDULED = 'scheduled';
    const PROCESSING = 'processing';
    const FAILED = 'failed';
    const COMPLETE = 'complete';

    private $id;
    private $corpus;
    private $added;
    private $complete;
    private $status;

    public function __construct($corpus)
    {
        $this->corpus = $corpus;
        $this->status = self::SCHEDULED;
        $this->added = new \Datetime('now');
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string The name for the corpus this scheduled index refers to.
     **/
    public function getCorpus()
    {
        return $this->corpus;
    }

    public function getAdded()
    {
        return $this->added;
    }

    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    public function getComplete()
    {
        return $this->complete;
    }

    public function setComplete($complete)
    {
        $this->complete = $complete;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function isValidStatus($v)
    {
        return in_array($v, [self::SCHEDULED, self::PROCESSING, self::FAILED, self::COMPLETE]);
    }

}
