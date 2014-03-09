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
     * @var AttributeInterface
     **/
    private $filter;

    /**
     * @param AttributeInterface $filter
     **/
    public function __construct(AttributeInterface $filter)
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

    public function getSearchKey(array $options = array())
    {
        return $this->filter->getSearchKey($options);
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
