<?php

namespace Markup\NeedleBundle\Attribute;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
 * An attribute implementation that allows a different name to the key.
 */
class Attribute implements AttributeInterface
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
     * @param string $name
     * @param string $key
     * @param
     **/
    public function __construct($name, $key = null, $displayName = null)
    {
        $this->name = $name;
        $this->key = $key ?: $name;
        $this->displayName = $displayName ?: ucfirst(str_replace('_', ' ', $name));
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getSearchKey(array $options = array())
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
}
