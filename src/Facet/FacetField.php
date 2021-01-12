<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A facet field implementation that uses a filter.
*/
class FacetField implements AttributeInterface
{
    /**
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
        return $this->getFilter()->getName();
    }

    public function getDisplayName()
    {
        return $this->getFilter()->getDisplayName();
    }

    public function getSearchKey(array $options = [])
    {
        return $this->getFilter()->getSearchKey($options);
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }

    /**
     * @return AttributeInterface
     **/
    public function getFilter()
    {
        return $this->filter;
    }
}
