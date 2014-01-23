<?php

namespace Markup\NeedleBundle\Provider;

use Markup\NeedleBundle\Facet\FacetField;

/**
 * A facet provider that consumes a filter provider and exposes its filters as facets.
 */
class ConsumeFiltersFacetProvider implements FacetProviderInterface
{
    /**
     * @var FilterProviderInterface
     */
    private $filterProvider;

    /**
     * @param FilterProviderInterface $filterProvider
     */
    public function __construct(FilterProviderInterface $filterProvider)
    {
        $this->filterProvider = $filterProvider;
    }

    /**
     * Gets a facet object using a name.  Returns false if name does not correspond to known facet.
     *
     * @param  string $name
     * @return \Markup\NeedleBundle\Facet\FacetInterface|bool
     **/
    public function getFacetByName($name)
    {
        $filter = $this->filterProvider->getFilterByName($name);
        if (!$filter) {
            return null;
        }

        return new FacetField($filter);
    }
}
