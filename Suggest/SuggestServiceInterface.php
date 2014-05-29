<?php

namespace Markup\NeedleBundle\Suggest;

use Markup\NeedleBundle\Query\SimpleQueryInterface;

/**
 * An interface for a service that can make a lookup for suggestions.
 */
interface SuggestServiceInterface
{
    /**
     * @param SimpleQueryInterface $query
     * @return SuggestResultInterface
     */
    public function fetchSuggestions(SimpleQueryInterface $query);
} 
