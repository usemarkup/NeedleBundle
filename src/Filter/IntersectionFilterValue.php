<?php

namespace Markup\NeedleBundle\Filter;

/**
* A filter value that represents the intersection of other filter values.
*/
class IntersectionFilterValue extends CombinedFilterValue implements IntersectionFilterValueInterface
{
    public function getValueType(): string
    {
        return self::TYPE_INTERSECTION;
    }
}
