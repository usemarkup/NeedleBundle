<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Filter\FilterInterface as Filter;

/**
* A facet field implementation that uses a filter.
*/
class FacetField implements FacetInterface
{
    /**
     * @var Filter
     **/
    private $filter;

    /**
     * @param Filter $filter
     **/
    public function __construct(Filter $filter)
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

    public function getSearchKey()
    {
        return $this->getFilter()->getSearchKey();
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }

    /**
     * @return Filter
     **/
    private function getFilter()
    {
        return $this->filter;
    }
}
