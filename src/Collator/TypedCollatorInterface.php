<?php

namespace Markup\NeedleBundle\Collator;

/**
 * An interface for a collator that is typed, i.e. has a specific domain of values it applies collation to.
 */
interface TypedCollatorInterface extends CollatorInterface
{
    /**
     * Gets the type (name) of this collator.
     *
     * @return string
     */
    public function getType();

    /**
     * Gets whether this collator has the type for the provided value (i.e. whether the value is in the type's domain).
     *
     * @param string $value
     * @return bool
     */
    public function hasTypeFor($value);
}
