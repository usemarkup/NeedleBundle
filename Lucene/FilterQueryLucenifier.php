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

    /**
     * @param FilterValueLucenifier $valueLucenifier
     **/
    public function __construct(FilterValueLucenifier $valueLucenifier)
    {
        $this->filterValueLucenifier = $valueLucenifier;
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
