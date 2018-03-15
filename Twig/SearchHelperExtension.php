<?php

namespace Markup\NeedleBundle\Twig;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface;

/**
* A Twig extension that provides some helper functions/filters for search.
*/
class SearchHelperExtension extends \Twig_Extension
{
    /**
     * @var FacetValueCanonicalizerInterface
     */
    private $canonicalizer;

    public function __construct(FacetValueCanonicalizerInterface $canonicalizer)
    {
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * {@inheritdoc}
     **/
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('needle_canonicalize_value_for_facet', [$this, 'canonicalizeForFacet']),
        ];
    }

    /**
     * Canonicalize a value for the provided facet.
     *
     * @param  string               $value
     * @param  AttributeInterface   $facet
     * @return string
     **/
    public function canonicalizeForFacet($value, $facet)
    {
        if (!$facet instanceof AttributeInterface) {
            return $value;
        }

        return $this->canonicalizer->canonicalizeForFacet($value, $facet);
    }

    public function getName()
    {
        return 'markup_needle.helper';
    }
}
