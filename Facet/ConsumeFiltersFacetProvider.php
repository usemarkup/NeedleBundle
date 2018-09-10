<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Exception\MissingAttributeException;

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
     * Gets a facet object using a name.  Returns null if name does not correspond to known facet.
     *
     * @param  string $name
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface|null
     **/
    public function getFacetByName($name)
    {
        try {
            $filter = $this->filterProvider->getAttributeByName($name);
        } catch (MissingAttributeException $e) {
            return null;
        }

        return new FacetField($filter);
    }
}
