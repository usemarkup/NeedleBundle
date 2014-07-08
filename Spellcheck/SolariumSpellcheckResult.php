<?php

namespace Markup\NeedleBundle\Spellcheck;

use Solarium\QueryType\Select\Result\Spellcheck\Result;
use Solarium\QueryType\Select\Result\Spellcheck\Suggestion;

/**
 * A spellcheck result wrapping the Solarium spellcheck result.
 */
class SolariumSpellcheckResult implements SpellcheckResultInterface
{
    /**
     * @var Result
     **/
    private $result;

    /**
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Gets whether the query for the spellcheck was correctly spelled.
     *
     * @var bool
     */
    public function isCorrectlySpelled()
    {
        return $this->result->getCorrectlySpelled();
    }

    /**
     * Gets a list of suggestions,
     *
     * @return string[]
     */
    public function getSuggestions()
    {
        return array_unique(array_reduce(
            $this->result->getSuggestions(),
            function ($carry, $item) {
                if (!$item instanceof Suggestion) {
                    return $carry;
                }

                return array_merge($carry, $item->getWords());
            },
            array()
        ));
    }
}
