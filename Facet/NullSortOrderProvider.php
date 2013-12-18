<?php

namespace Markup\NeedleBundle\Facet;

/**
* A null sort order provider implementation.
*/
class NullSortOrderProvider implements SortOrderProviderInterface
{
    /**
     * {@inheritdoc}
     **/
    public function getSortOrderForFacet(FacetInterface $facet)
    {
        return null;
    }
}
