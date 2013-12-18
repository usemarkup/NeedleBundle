<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Filter\FilterInterface;

/**
* A range facet implementation.
*/
class RangeFacetField extends FacetField implements RangeFacetInterface
{
    /**
     * @var RangeFacetConfigurationInterface
     **/
    private $rangeFacetConfiguration;

    /**
     * @param FilterInterface                  $filter
     * @param RangeFacetConfigurationInterface $rangeFacetConfig
     **/
    public function __construct(FilterInterface $filter, RangeFacetConfigurationInterface $rangeFacetConfig)
    {
        parent::__construct($filter);
        $this->rangeFacetConfiguration = $rangeFacetConfig;
    }

    public function getRangeSize()
    {
        return $this->getConfiguration()->getGap();
    }

    public function getRangesStart()
    {
        return $this->getConfiguration()->getStart();
    }

    public function getRangesEnd()
    {
        return $this->getConfiguration()->getEnd();
    }

    /**
     * @return RangeFacetConfigurationInterface
     **/
    private function getConfiguration()
    {
        return $this->rangeFacetConfiguration;
    }
}
