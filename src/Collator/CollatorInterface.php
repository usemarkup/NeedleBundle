<?php

namespace Markup\NeedleBundle\Collator;

/**
 * An interface for a collator (sorter).
 **/
interface CollatorInterface
{
    /**
     * Compare two values on a linear scale.
     *
     * @param mixed $value1
     * @param mixed $value2
     *
     * @return int Returns 1 if first operand is greater than second, 0 if they are equal, -1 if first operand less than second.
     **/
    public function compare($value1, $value2);
}
