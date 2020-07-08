<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * An attribute implementation that allows a different name to the key.
 */
class Attribute implements AttributeInterface, AttributeValueOptionsInterface
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
     * @var bool
     */
    private $shouldCanonicalizeDisplayValue;

    public function __construct(
        string $name,
        string $key = null,
        string $displayName = null,
        bool $shouldCanonicalizeDisplayValue = false
    ) {
        $this->name = $name;
        $this->key = $key ?: $name;
        $this->displayName = $displayName ?: ucfirst(str_replace('_', ' ', $name));
        $this->shouldCanonicalizeDisplayValue = $shouldCanonicalizeDisplayValue;
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

    public function shouldCanonicalizeDisplayValue(): bool
    {
        return $this->shouldCanonicalizeDisplayValue;
    }
}
