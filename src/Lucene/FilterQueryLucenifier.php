<?php

namespace Markup\NeedleBundle\Lucene;

use Markup\NeedleBundle\Filter\FilterValueInterface;

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

    public function lucenify(string $searchKey, FilterValueInterface $filterValue): string
    {
        if ($filterValue->getSearchValue() === '') {
            return sprintf('-%s:[* TO *]', $searchKey);
        }

        return sprintf('%s:%s', $searchKey, $this->filterValueLucenifier->lucenify($filterValue));
    }
}
