<?php

namespace Markup\NeedleBundle\Twig;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* A Twig extension that provides some helper functions/filters for search.
*/
class SearchHelperExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     **/
    private $container;

    /**
     * @param ContainerInterface $container
     **/
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     **/
    public function getFilters()
    {
        return [
            'needle_canonicalize_value_for_facet'      => new \Twig_Filter_Method($this, 'canonicalizeForFacet'),
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

        return $this->getFacetValueCanonicalizer()->canonicalizeForFacet($value, $facet);
    }

    public function getName()
    {
        return 'markup_needle.helper';
    }

    /**
     * Gets the facet value canonicalizer service.
     *
     * @return FacetValueCanonicalizerInterface
     **/
    private function getFacetValueCanonicalizer()
    {
        return $this->container->get('markup_needle.facet.value_canonicalizer');
    }
}
