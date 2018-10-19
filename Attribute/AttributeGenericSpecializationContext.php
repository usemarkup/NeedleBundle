<?php

namespace Markup\NeedleBundle\Attribute;

use Markup\NeedleBundle\Exception\IllegalContextValueException;

/**
 * An implementation that attempts to turn the passed variable into a string using a few simple strategies
 */
class AttributeGenericSpecializationContext implements AttributeSpecializationContextInterface
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        if (is_string($this->data) || is_numeric($this->data)) {
            return (string) $this->data;
        }
        if (is_object($this->data)) {
            if (method_exists($this->data, 'getKey')) {
                return $this->data->getKey();
            }
            if (method_exists($this->data, 'getName')) {
                return $this->data->getName();
            }
            if (method_exists($this->data, 'getValue')) {
                return $this->data->getValue();
            }
        }
        throw new IllegalContextValueException($this->data, 'Cannot get a value for the given data');
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
