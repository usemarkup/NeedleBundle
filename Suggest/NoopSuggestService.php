<?php

namespace Markup\NeedleBundle\Suggest;

use Markup\NeedleBundle\Query\SimpleQueryInterface;

class NoopSuggestService implements SuggestServiceInterface
{
    /**
     * @param SimpleQueryInterface $query
     * @return SuggestResultInterface
     */
    public function fetchSuggestions(SimpleQueryInterface $query)
    {
        return new EmptySuggestResult();
    }
}
