<?php

namespace Markup\NeedleBundle\Spellcheck;

class Suggestion implements \Countable
{
    /**
     * @var string
     */
    private $word;

    /**
     * @var int
     */
    private $frequency;

    /**
     * @param string $word
     * @param int    $frequency
     */
    public function __construct($word, $frequency)
    {
        $this->word = $word;
        $this->frequency = intval($frequency);
    }

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    public function __toString()
    {
        return $this->getWord();
    }

    /**
     * @return int
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return $this->getFrequency();
    }
}
