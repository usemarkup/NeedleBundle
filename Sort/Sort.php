<?php

namespace Markup\NeedleBundle\Sort;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A simple sort.
*/
class Sort implements SortInterface
{
    /**
     * @var AttributeInterface
     **/
    private $filter;

    /**
     * @var bool
     **/
    private $isDescending;

    /**
     * @param AttributeInterface $filter
     * @param bool               $isDescending
     **/
    public function __construct(AttributeInterface $filter, $isDescending = false)
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
