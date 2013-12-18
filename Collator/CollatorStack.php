<?php

namespace Markup\NeedleBundle\Collator;

use SplStack;

/**
 * A collator that composes a stack of typed collators (i.e. the order of the collators is significant, and a value of an earlier type will be sorted to before one of a later type).
 */
class CollatorStack implements CollatorInterface
{
    /**
     * @var SplStack<TypedCollatorInterface>
     */
    private $collators;

    /**
     * Whether values for which there is no type (and therefore no usable collator) are sorted to the beginning of the list (true) or at the end (false).
     *
     * @var bool
     */
    private $shouldUntypedValuesPrecede;

    public function __construct()
    {
        $this->collators = new SplStack();
        $this->shouldUntypedValuesPrecede = false;
    }

    /**
     * {@inheritdoc}
     **/
    public function compare($value1, $value2)
    {
        $type1 = $this->getCollatorIndexFor($value1);
        $type2 = $this->getCollatorIndexFor($value2);

        //if we have known types
        if (null !== $type1 && null !== $type2) {
            if ($type1 !== $type2) {
                return $type1-$type2;
            } else {
                return $this->collators->offsetGet($type1)->compare($value1, $value2);
            }
        }
        //if both are null
        if (null === $type1 && null === $type2) {
            return strcasecmp($value1, $value2);
        }

        $order = (null === $type1) ? 1 : -1;
        if ($this->shouldUntypedValuesPrecede) {
            //flip the polarity
            return -$order;
        }

        return $order;
    }

    /**
     * Pushes a typed collator onto this stack.
     *
     * @param TypedCollatorInterface $collator
     * @return self
     */
    public function push(TypedCollatorInterface $collator)
    {
        $this->collators->push($collator);

        return $this;
    }

    /**
     * Sets whether values for which there is no type (and therefore no usable collator) are sorted to the beginning of the list (true) or at the end (false).
     *
     * @param bool $whether
     * @return self
     */
    public function setUntypedValuesToPrecede($whether)
    {
        $this->shouldUntypedValuesPrecede = $whether;

        return $this;
    }

    /**
     * Gets the index of the collator whose type matches the provided value.  If there is no matched type, returns null.
     *
     * @param string $value
     * @return int|null
     */
    private function getCollatorIndexFor($value)
    {
        foreach ($this->collators as $index => $collator) {
            if ($collator->hasTypeFor($value)) {
                return $index;
            }
        }
    }
}
