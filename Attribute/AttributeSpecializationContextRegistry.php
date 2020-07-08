<?php

namespace Markup\NeedleBundle\Attribute;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Markup\NeedleBundle\Exception\UnrecognizedSpecializationException;

class AttributeSpecializationContextRegistry implements AttributeSpecializationContextRegistryInterface
{
    /**
     * @var Collection|AttributeSpecializationContextProviderInterface[]
     */
    private $attributeSpecializationContextProviders;

    public function __construct()
    {
        $this->attributeSpecializationContextProviders = new ArrayCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function getContextForData(
        string $specializationName,
        $data
    ): AttributeSpecializationContextInterface {
        foreach ($this->attributeSpecializationContextProviders as $provider) {
            if ($provider->getSpecialization()->getName() == $specializationName) {
                foreach ($provider->getContexts() as $context) {
                    if (is_string($context)) {
                        $context = new AttributeGenericSpecializationContext($context);
                    }

                    if ($context instanceof AttributeSpecializationContextInterface) {
                        if (is_string($data) && strcasecmp($context->getData(), $data) === 0) {
                            return $context;
                        }

                         if ($context->getData() == $data) {
                            return $context;
                        }
                    }
                }
            }
        }

        throw new UnrecognizedSpecializationException(
            sprintf('Specialization `%s` is not recognized for data `%s`', $specializationName, $data)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getContexts(string $specializationName)
    {
        foreach ($this->attributeSpecializationContextProviders as $provider) {
            if ($provider->getSpecialization()->getName() === $specializationName) {
                return $provider->getContexts();
            }
        }
        throw new UnrecognizedSpecializationException(
            sprintf('Specialization %s is not recognized', $specializationName)
        );
    }

    /**
     * @param AttributeSpecializationContextProviderInterface $provider
     */
    public function addAttributeSpecializationContextProvider(AttributeSpecializationContextProviderInterface $provider)
    {
        $this->attributeSpecializationContextProviders->add($provider);
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecializationContextHashAsScalarArray(SpecializationContextHashInterface $contextHash)
    {
        $hash = [];
        foreach ($this->attributeSpecializationContextProviders as $provider) {
            $specialization = $provider->getSpecialization();
            $hash[$specialization->getName()] = $this->getContextForData(
                $specialization->getName(),
                $contextHash->toArray()[$specialization->getName()]
            )->getValue();
        }

        return $hash;
    }

    /**
     * @throws UnrecognizedSpecializationException If hash contains a value not contained in the system registries
     */
    public function validateSpecializationContextHash(SpecializationContextHash $specializationContextHash): void
    {
        foreach ($specializationContextHash->toArray() as $specializationName => $data) {
            $this->getContextForData($specializationName, $data);
        }
    }
}
