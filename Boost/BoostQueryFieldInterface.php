<?php

namespace Markup\NeedleBundle\Boost;

/**
 * An interface for a field query boost, i.e. a weighting factor for an individual attribute.
 **/
interface BoostQueryFieldInterface
{
    /**
     * Gets the key for the attribute being boosted.
     *
     * @return string
     **/
    public function getAttributeKey();

    /**
     * Gets the boost factor for this attribute.
     *
     * @return float
     **/
    public function getBoostFactor();
}
