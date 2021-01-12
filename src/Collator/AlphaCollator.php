<?php

namespace Markup\NeedleBundle\Collator;

/**
 * A typed collator that can sort by alphabetical order (case insensitive).
 */
class AlphaCollator implements TypedCollatorInterface
{
    const TYPE = 'alpha';

    /**
     * Compare two values on a linear scale.
     *
     * @param mixed $value1
     * @param mixed $value2
     *
     * @return int Returns 1 if first operand is greater than second, 0 if they are equal, -1 if first operand less than second.
     **/
    public function compare($value1, $value2)
    {
        $cmp = strcasecmp($value1, $value2);
        if ($cmp === 0) {
            //return 0 early to prevent division by zero
            return 0;
        }

        return $cmp/abs($cmp);
    }

    /**
     * Gets the type (name) of this collator.
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Gets whether this collator has the type for the provided value (i.e. whether the value is in the type's domain).
     *
     * @param string $value
     * @return bool
     */
    public function hasTypeFor($value)
    {
        //we can sort anything!
        return true;
    }
}
