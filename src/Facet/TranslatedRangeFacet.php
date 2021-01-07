<?php

namespace Markup\NeedleBundle\Facet;

use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
* A range facet that has a translated display name.
*/
class TranslatedRangeFacet extends TranslatedFacet implements RangeFacetInterface
{
    /**
     * The range facet specific configuration.
     *
     * @var RangeFacetConfigurationInterface
     **/
    private $rangeFacetConfiguration;

    /**
     * @param string     $name
     * @param Translator $translator
     * @param string     $translationNamespace
     * @param string     $messageDomain
     * @param string     $searchKey
     **/
    public function __construct(
        $name,
        RangeFacetConfigurationInterface $rangeConfiguration,
        Translator $translator,
        $translationNamespace,
        $messageDomain = null,
        $searchKey = null
    ) {
        parent::__construct($name, $translator, $translationNamespace, $messageDomain, $searchKey);
        $this->rangeFacetConfiguration = $rangeConfiguration;
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
