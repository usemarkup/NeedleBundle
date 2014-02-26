<?php

namespace Markup\NeedleBundle\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A superclass for a decorator for filters, setting up the default behaviour of a 1:1 map.
*/
abstract class FilterDecorator implements AttributeInterface
{
    /**
     * The filter being decorated.
     *
     * @var FilterInterface
     **/
    private $filter;

    /**
     * @param FilterInterface $filter
     **/
    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    public function getName()
    {
        return $this->filter->getName();
    }

    public function getDisplayName()
    {
        return $this->filter->getDisplayName();
    }

    public function getSearchKey()
    {
        return $this->filter->getSearchKey();
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
