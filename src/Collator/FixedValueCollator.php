<?php

namespace Markup\NeedleBundle\Collator;

/**
* A collator that compares based on a list of values.
*/
class FixedValueCollator implements CollatorInterface
{
    /**
     * The fixed list of values.
     *
     * @var array
     **/
    private $values;

    /**
     * @param array $values The fixed list of values to collate on.
     **/
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     **/
    public function compare($value1, $value2)
    {
        $position1 = array_search($value1, $this->values);
        $position2 = array_search($value2, $this->values);
        if (false !== $position1 && false !== $position2) {
            //both are in list
            $difference = intval($position1) - intval($position2);
            if ($difference === 0) {
                return 0;
            }

            return $difference/abs($difference);
        }

        //check if both are outside list
        if (false === $position1 && false === $position2) {
            return strcasecmp($value1, $value2);
        }
        if (false === $position1) {
            return 1;
        }

        return -1;
    }
}
