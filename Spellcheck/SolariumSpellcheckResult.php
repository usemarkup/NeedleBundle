<?php

namespace Markup\NeedleBundle\Spellcheck;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Solarium\QueryType\Select\Result\Spellcheck\Result;
use Solarium\QueryType\Select\Result\Spellcheck\Suggestion as SolariumSuggestion;

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
     * @var SimpleQueryInterface
     */
    private $query;

    /**
     * @param Result $result
     * @param SimpleQueryInterface $query
     */
    public function __construct(Result $result, SimpleQueryInterface $query)
    {
        $this->result = $result;
        $this->query = $query;
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
     * Gets a list of suggestions.
     *
     * @return Suggestion[]
     */
    public function getSuggestions()
    {
        $that = $this;
        return array_values(array_unique(array_filter(array_map(function ($item) {
            if (!$item instanceof SolariumSuggestion) {
                return null;
            }

            return new Suggestion($item->getWord(), $item->getNumFound());
        }, $this->result->getSuggestions()), function (Suggestion $suggestion = null) use ($that) {
            if (!$suggestion || !$suggestion->getWord()) {
                return false;
            }
            if (!$that->query->hasSearchTerm()) {
                return true;
            }

            return $suggestion->getWord() !== $that->query->getSearchTerm();
        })));
    }
}
