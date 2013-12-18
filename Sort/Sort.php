<?php

namespace Markup\NeedleBundle\Sort;

use Markup\NeedleBundle\Filter\FilterInterface;

/**
* A simple sort.
*/
class Sort implements SortInterface
{
    /**
     * @var FilterInterface
     **/
    private $filter;

    /**
     * @var bool
     **/
    private $isDescending;

    /**
     * @param FilterInterface $filter
     * @param bool            $isDescending
     **/
    public function __construct(FilterInterface $filter, $isDescending = false)
    {
        $this->filter = $filter;
        $this->isDescending = (bool) $isDescending;
    }

    /**
     * {@inheritdoc}
     **/
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     **/
    public function isDescending()
    {
        return $this->isDescending;
    }
}
