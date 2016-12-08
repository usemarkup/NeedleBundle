<?php

namespace Markup\NeedleBundle\Attribute;

use Doctrine\Common\Collections\ArrayCollection;
use Markup\NeedleBundle\Exception\UnrecognizedSpecializationException;

class AttributeSpecializationContextRegistry implements AttributeSpecializationContextRegistryInterface
{

    /**
     * @var AttributeSpecializationContextProviderInterface[]
     */
    private $attributeSpecializationContextProviders;

    /**
     * @var AttributeSpecializationCurrentlyApplicableContextProviderInterface[]
     */
    private $attributeSpecializationCurrentlyApplicableContextProviders;

    public function __construct()
    {
        $this->attributeSpecializationContextProviders = new ArrayCollection();
        $this->attributeSpecializationCurrentlyApplicableContextProviders = new ArrayCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function getContext(AttributeSpecialization $specialization)
    {
        foreach ($this->attributeSpecializationCurrentlyApplicableContextProviders as $provider) {
            if ($provider->getSpecialization()->getName() === $specialization->getName()) {
                return $provider->getContext();
            }
        }
        throw new UnrecognizedSpecializationException(sprintf('Specialization %s is not recognized', $specialization->getName()));
    }

    /**
     * {@inheritDoc}
     */
    public function getContexts(AttributeSpecialization $specialization)
    {
        foreach ($this->attributeSpecializationContextProviders as $provider) {
            if ($provider->getSpecialization()->getName() === $specialization->getName()) {
                return $provider->getContexts();
            }
        }
        throw new UnrecognizedSpecializationException(sprintf('Specialization %s is not recognized', $specialization->getName()));
    }

    /**
     * @param AttributeSpecializationContextProviderInterface $provider
     */
    public function addAttributeSpecializationContextProvider(AttributeSpecializationContextProviderInterface $provider)
    {
        $this->attributeSpecializationContextProviders->add($provider);
    }

    /**
     * @param AttributeSpecializationCurrentlyApplicableContextProviderInterface $provider
     */
    public function addAttributeSpecializationCurrentlyApplicableContextProvider(AttributeSpecializationCurrentlyApplicableContextProviderInterface $provider)
    {
        $this->attributeSpecializationCurrentlyApplicableContextProviders->add($provider);
    }

    /**
     * Gets a hash of all current SpecializationContexts with currently applicable values
     */
    public function getSpecializationContextHash()
    {
        $hash = [];
        foreach($this->attributeSpecializationContextProviders as $provider) {
            $specialization = $provider->getSpecialization();
            $hash[$specialization->getName()] = $this->getContext($specialization)->getValue();
        }
        return $hash;
    }
}
