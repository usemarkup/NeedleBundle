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
     * @param AttributeSpecializationCurrentlyProviderInterface $provider
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
}
