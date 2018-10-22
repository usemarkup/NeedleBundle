<?php

namespace Markup\NeedleBundle\Lucene;

use Markup\NeedleBundle\Filter\FilterQueryInterface;

/**
* An object that can lucenify filter queries.
*/
class FilterQueryLucenifier
{
    /**
     * @var FilterValueLucenifier
     **/
    private $filterValueLucenifier;

    public function __construct(?FilterValueLucenifier $valueLucenifier = null)
    {
        $this->filterValueLucenifier = $valueLucenifier ?? new FilterValueLucenifier();
    }

    /**
     * Lucenifies that provided filter query.
     *
     * @param  FilterQueryInterface $filterQuery
     * @return string
     **/
    public function lucenify(FilterQueryInterface $filterQuery)
    {
        if ($filterQuery->getFilterValue()->getSearchValue() === '') {
            return sprintf('-%s:[* TO *]', $filterQuery->getSearchKey());
        }

        return sprintf('%s:%s', $filterQuery->getSearchKey(), $this->filterValueLucenifier->lucenify($filterQuery->getFilterValue()));
    }
}
