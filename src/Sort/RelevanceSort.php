<?php

namespace Markup\NeedleBundle\Sort;

use Markup\NeedleBundle\Filter\SimpleFilter;

/**
* A sort on relevance.
*/
class RelevanceSort extends Sort
{
    const RELEVANCE_FILTER_NAME = 'score';

    public function __construct()
    {
        parent::__construct(new SimpleFilter(self::RELEVANCE_FILTER_NAME), true);
    }
}
