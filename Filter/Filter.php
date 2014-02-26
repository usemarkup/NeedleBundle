<?php

namespace Markup\NeedleBundle\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
 * A filter implementation that allows a different name to the key.
 */
class Filter implements AttributeInterface
{
    /**
     * The name for the filter.
     *
     * @var string
     **/
    private $name;

    /**
     * The key for the filter.
     *
     * @var string
     */
    private $key;

    /**
     * The display name for the filter.
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

    public function getSearchKey()
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
