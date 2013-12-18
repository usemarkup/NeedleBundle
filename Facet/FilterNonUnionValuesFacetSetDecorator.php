<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
 * A facet set decorator that filters out values that, in a case where an original (implicit) query contained combined filter values for a given attribute, are not included as one of these filter values.
 */
class FilterNonUnionValuesFacetSetDecorator extends FacetSetDecorator
{
    /**
     * @var SelectQueryInterface
     */
    private $originalQuery;

    /**
     * @param SelectQueryInterface $originalQuery
     */
    public function __construct(SelectQueryInterface $originalQuery = null)
    {
        $this->originalQuery = $originalQuery;
    }

    public function getIterator()
    {
        if (null === $this->facetSet) {
            throw new \LogicException('A facet set should be set on this decorator.');
        }

        return new NonUnionValueFilterIterator(parent::getIterator(), $this->facetSet, $this->originalQuery);
    }

    public function count()
    {
        return count(iterator_to_array($this));
    }
}
