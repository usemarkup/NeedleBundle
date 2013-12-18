<?php

namespace Markup\NeedleBundle\Collator;

/**
 * A collator that uses a word list using known words.
 */
class WordListCollator implements TypedCollatorInterface
{
    const TYPE = 'word_list';

    /**
     * @var array
     */
    private $words;

    /**
     * @param array $words
     */
    public function __construct(array $words)
    {
        $this->words = $words;
    }

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
        $index1 = array_search($value1, $this->words);
        $index2 = array_search($value2, $this->words);
        if ($index1 > $index2) {
            return 1;
        } elseif ($index1 == $index2) {
            return 0;
        } else {
            return -1;
        }
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
        return false !== array_search($value, $this->words);
    }
}
