<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * An attribute implementation that allows a different name to the key.
 */
class Attribute implements AttributeInterface, AttributeProvidesValueDisplayStrategyInterface
{
    /**
     * The name for the attribute.
     *
     * @var string
     **/
    private $name;

    /**
     * The key for the attribute.
     *
     * @var string
     */
    private $key;

    /**
     * The display name for the attribute.
     *
     * @var string
     */
    private $displayName;

    /**
     * The value display strategy for the attribute.
     *
     * @var callable
     */
    private $valueDisplayStrategy;

    /**
     * @param string   $name
     * @param string   $key
     * @param string   $displayName
     * @param \Closure $valueDisplayStrategy
     **/
    public function __construct($name, $key = null, $displayName = null, callable $valueDisplayStrategy = null)
    {
        $this->name = $name;
        $this->key = $key ?: $name;
        $this->displayName = $displayName ?: ucfirst(str_replace('_', ' ', $name));
        $this->valueDisplayStrategy = $valueDisplayStrategy ?: function ($value) {
            return $value;
        };
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getSearchKey(array $options = [])
    {
        return $this->key;
    }

    /**
     * Magic toString method.  Returns display name.
     *
     * @return string
     **/
    public function __toString()
    {
        return $this->getDisplayName();
    }

    /**
     * Gets a display strategy closure which takes a value as a parameter and emits a displayable value.
     *
     * @return \Closure|null
     */
    public function getValueDisplayStrategy()
    {
        return $this->valueDisplayStrategy;
    }
}
