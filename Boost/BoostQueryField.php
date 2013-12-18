<?php

namespace Markup\NeedleBundle\Boost;

/**
* A boost query field, representing the weighting to put on a particular attribute in a search.
*/
class BoostQueryField implements BoostQueryFieldInterface
{
    /**
     * The key for the attribute being boosted.
     *
     * @var string
     **/
    private $attributeKey;

    /**
     * The boost factor that acts as a multiplier for the attribute.
     *
     * @var float
     **/
    private $boostFactor;

    /**
     * @param string $attributeKey
     * @param float  $boostFactor
     **/
    public function __construct($attributeKey, $boostFactor = 1)
    {
        $this->attributeKey = $attributeKey;
        $this->boostFactor = $boostFactor;
    }

    public function getAttributeKey()
    {
        return $this->attributeKey;
    }

    public function getBoostFactor()
    {
        return $this->boostFactor;
    }
}
