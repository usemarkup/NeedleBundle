<?php

namespace Markup\NeedleBundle\Boost;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A boost query field, representing the weighting to put on a particular attribute in a search.
*/
class BoostQueryField implements BoostQueryFieldInterface
{
    /**
     * The attribute being boosted.
     *
     * @var AttributeInterface
     **/
    private $attribute;

    /**
     * The boost factor that acts as a multiplier for the attribute.
     *
     * @var float
     **/
    private $boostFactor;

    /**
     * @param AttributeInterface $attribute
     * @param float              $boostFactor
     **/
    public function __construct(AttributeInterface $attribute, $boostFactor = 1.0)
    {
        $this->attribute = $attribute;
        $this->boostFactor = $boostFactor;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function getBoostFactor()
    {
        return $this->boostFactor;
    }
}
