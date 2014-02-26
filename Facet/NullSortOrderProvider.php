<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A null sort order provider implementation.
*/
class NullSortOrderProvider implements SortOrderProviderInterface
{
    /**
     * {@inheritdoc}
     **/
    public function getSortOrderForFacet(AttributeInterface $facet)
    {
        return null;
    }
}
