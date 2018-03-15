<?php

namespace Markup\NeedleBundle\Lucene;

use Markup\NeedleBundle\Filter;

/**
* An object that can turn a filter value into a Lucene expression.
*/
class FilterValueLucenifier
{
    /**
     * @var HelperInterface
     **/
    private $helper;

    /**
     * @param HelperInterface $helper
     **/
    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     **/
    public function lucenify(Filter\FilterValueInterface $filterValue)
    {
        //if the filterValue is a union, iterate over it to form a query with an OR operator
        if ($filterValue instanceof Filter\CombinedFilterValueInterface) {
            $luceneParts = [];
            foreach ($filterValue as $subValue) {
                $luceneParts[] = $this->lucenify($subValue);
            }

            if ($filterValue instanceof Filter\IntersectionFilterValueInterface) {
                $luceneParts = array_map(function ($v) {
                    return '+'.$v;
                }, $luceneParts);
            }

            return '('.implode(' ', $luceneParts).')';
        }

        //perform phrase escaping unless this is a range filter (and therefore we need to preserve lucene syntax)
        return (!$filterValue instanceof Filter\RangeFilterValueInterface) ? $this->helper->assemble('%P1%', [$filterValue->getSearchValue()]) : $filterValue->getSearchValue();
    }
}
