<?php

namespace Markup\NeedleBundle\Boost;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
 * An interface for a field query boost, i.e. a weighting factor for an individual attribute.
 **/
interface BoostQueryFieldInterface
{
    /**
     * Gets the attribute being boosted.
     *
     * @return AttributeInterface
     **/
    public function getAttribute();

    /**
     * Gets the boost factor for this attribute.
     *
     * @return float
     **/
    public function getBoostFactor();
}
