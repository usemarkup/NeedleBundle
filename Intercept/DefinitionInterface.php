<?php

namespace Markup\NeedleBundle\Intercept;

/**
 * An interface for an individual search intercept definition.
 **/
interface DefinitionInterface
{
    /**
     * Gets a matcher that match a term to the definition.
     *
     * @return MatcherInterface
     **/
    public function getMatcher();

    /**
     * Gets the type of definition.
     *
     * @return string
     **/
    public function getType();

    /**
     * Gets the definition's name.
     *
     * @return string
     **/
    public function getName();

    /**
     * Gets the different properties of the definition.
     *
     * @return array
     **/
    public function getProperties();
}
