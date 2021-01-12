<?php

namespace Markup\NeedleBundle\Spellcheck;

/**
 * An object encapsulating a request to perform a spellcheck, if one is found against a query.
 */
class Spellcheck implements SpellcheckInterface
{
    /**
     * @var DictionaryProviderInterface
     */
    private $dictionaryProvider;

    /**
     * @var int|null
     */
    private $resultLimit;

    /**
     * @param DictionaryProviderInterface $dictionaryProvider
     * @param int|null                    $resultLimit
     */
    public function __construct(DictionaryProviderInterface $dictionaryProvider, $resultLimit = null)
    {
        $this->dictionaryProvider = $dictionaryProvider;
        $this->resultLimit = $resultLimit;
    }

    /**
     * Gets the maximum count of spellcheck results, if one is set (otherwise returns null).
     *
     * @return int|null
     */
    public function getResultLimit()
    {
        return $this->resultLimit;
    }

    /**
     * Gets the spellcheck dictionary to use for a query.
     *
     * @return string
     */
    public function getDictionary()
    {
        return $this->dictionaryProvider->getDictionary();
    }
}
