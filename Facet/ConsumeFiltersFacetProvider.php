<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;

/**
 * A facet provider that consumes a filter provider and exposes its filters as facets.
 */
class ConsumeFiltersFacetProvider implements FacetProviderInterface
{
    /**
     * @var AttributeProviderInterface
     */
    private $filterProvider;

    /**
     * @param AttributeProviderInterface $filterProvider
     */
    public function __construct(AttributeProviderInterface $filterProvider)
    {
        $this->filterProvider = $filterProvider;
    }

    /**
     * Gets a facet object using a name.  Returns false if name does not correspond to known facet.
     *
     * @param  string $name
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface|bool
     **/
    public function getFacetByName($name)
    {
        $filter = $this->filterProvider->getAttributeByName($name);
        if (!$filter) {
            return null;
        }

        return new FacetField($filter);
    }
}
