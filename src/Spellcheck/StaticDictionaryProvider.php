<?php

namespace Markup\NeedleBundle\Spellcheck;

class StaticDictionaryProvider implements DictionaryProviderInterface
{
    /**
     * @var string
     */
    private $dictionary;

    /**
     * @param string $dictionary
     */
    public function __construct($dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @return string
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }
}
