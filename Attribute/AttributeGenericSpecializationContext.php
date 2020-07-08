<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Attribute;

/**
 * An implementation that attempts to turn the passed variable into a string using a few simple strategies
 */
class AttributeGenericSpecializationContext implements AttributeSpecializationContextInterface
{
    /**
     * @var string
     */
    private $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): string
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
