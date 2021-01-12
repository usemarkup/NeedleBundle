<?php

namespace Markup\NeedleBundle\Filter;

/**
* A filter query with simple parameters to the constructor.
*/
class SimpleFilterQuery extends FilterQuery
{
    /**
     * @param string $filterKey
     * @param string $filterValue
     **/
    public function __construct($filterKey, $filterValue)
    {
        parent::__construct(new SimpleFilter($filterKey), new ScalarFilterValue($filterValue));
    }
}
