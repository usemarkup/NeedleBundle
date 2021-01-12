<?php

namespace Markup\NeedleBundle\Filter;

/**
* A filter value that represents the union of other filter values.
*/
class UnionFilterValue extends CombinedFilterValue implements UnionFilterValueInterface
{
    public function getValueType(): string
    {
        return self::TYPE_UNION;
    }
}
