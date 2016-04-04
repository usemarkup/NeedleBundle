<?php

namespace Markup\NeedleBundle\Attribute;

use Markup\NeedleBundle\Exception\IllegalContextValueException;
use Markup\NeedleBundle\Exception\UnformableSearchKeyException;

/**
 * An attribute implementation that has a specialization set on it
 * allowing the search key to be changed by adding a context
 */
class SpecializedAttribute extends Attribute implements SpecializedAttributeInterface
{
    /**
     * @var AttributeSpecializationInterface
     */
    protected $specialization;

    /**
     * @var AttributeSpecializationContextInterface
     */
    protected $context;

    /**
     * @param AttributeSpecializationInterface $specialization
     * @param string                           $name
     * @param string                           $key
     * @param string                           $displayName
     * @param callable|null                    $valueDisplayStrategy
     */
    public function __construct(
        AttributeSpecializationInterface $specialization,
        $name,
        $key = null,
        $displayName = null,
        callable $valueDisplayStrategy = null
    ) {
        $this->specialization = $specialization;
        parent::__construct($name, $key, $displayName, $valueDisplayStrategy);
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * {@inheritDoc}
     */
    public function setContext(AttributeSpecializationContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritDoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchKey(array $options = [])
    {
        if (!$this->context) {
            throw new \LogicException(sprintf('Cannot get a search key for a specialized attribute without a context where name is %s', $this->getName()));
        }

        if ($this->context instanceof AttributeSpecializationNullContextInterface) {
            return parent::getSearchKey();
        }
        try {
            $contextValue = $this->context->getValue();
        } catch (IllegalContextValueException $e) {
            throw new UnformableSearchKeyException(sprintf('The attribute "%s" cannot form a search key due to incomplete context data.', $this->getName()));
        }

        return sprintf('%s_%s', parent::getSearchKey(), $contextValue);
    }
}
