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
     * @var AttributeSpecializationInterface[]
     */
    protected $specializations;

    /**
     * @var AttributeSpecializationContextInterface[]
     */
    protected $contexts;

    /**
     * @param AttributeSpecializationInterface[] $specializations
     * @param string                           $name
     * @param string                           $key
     * @param string                           $displayName
     * @param callable|null                    $valueDisplayStrategy
     */
    public function __construct(
        array $specializations,
        string $name,
        string $key = null,
        string $displayName = null,
        callable $valueDisplayStrategy = null
    ) {
        $this->specializations = $specializations;
        $this->contexts = [];
        parent::__construct($name, $key, $displayName, $valueDisplayStrategy);
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecializations()
    {
        return $this->specializations;
    }

    /**
     * {@inheritDoc}
     */
    public function setContext(
        AttributeSpecializationContextInterface $context,
        string $specialization
    ) {
        $this->contexts[$specialization] = $context;
    }

    /**
     * {@inheritDoc}
     */
    public function getContext(string $specialization)
    {
        if (!array_key_exists($specialization, $this->contexts)) {
            throw new \LogicException(
                'Cannot get context for specialization `%s` on attribute `%s` as it has not been set',
                $specialization,
                $this->getName()
            );
        }

        return $this->contexts[$specialization];
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchKey(array $options = [])
    {
        foreach ($this->specializations as $specialization) {
            if (!array_key_exists($specialization->getName(), $this->contexts)) {
                throw new \LogicException(
                    sprintf(
                        'Cannot get context for specialization `%s` on attribute `%s` as it has not been set',
                        $specialization->getName(),
                        $this->getName()
                    )
                );
            }
        }

        ksort($this->contexts);
        $contextValues = [];

        foreach ($this->contexts as $context) {
            if ($context instanceof AttributeSpecializationNullContextInterface) {
                continue;
            }

            try {
                $contextValues[] = $context->getValue();
            } catch (IllegalContextValueException $e) {
                throw new UnformableSearchKeyException(
                    sprintf(
                        'The attribute "%s" cannot form a search key due to incomplete context data.',
                        $this->getName())
                );
            }
        }

        if (empty($contextValues)) {
            return parent::getSearchKey();
        }

        return sprintf('%s_%s', parent::getSearchKey(), implode('_', $contextValues));
    }
}
