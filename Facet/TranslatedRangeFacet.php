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
     * @param float      $range_size
     * @param Translator $translator
     * @param string     $translation_namespace
     * @param string     $message_domain
     * @param string     $search_key
     **/
    public function __construct($name, RangeFacetConfigurationInterface $range_configuration, Translator $translator, $translation_namespace, $message_domain = null, $search_key = null)
    {
        parent::__construct($name, $translator, $translation_namespace, $message_domain, $search_key);
        $this->rangeFacetConfiguration = $range_configuration;
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
