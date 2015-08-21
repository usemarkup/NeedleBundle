<?php

namespace Markup\NeedleBundle\Attribute;

use Doctrine\Common\Collections\ArrayCollection;

class AttributeSpecializationProvider extends ArrayCollection implements AttributeSpecializationProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getSpecializations()
    {
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecialization($name)
    {
        foreach ($this->toArray() as $specialization) {
            if ($name === $specialization->getName()) {
                return $specialization;
            }
        }

        return;
    }

    /**
     * @param array $names A collection of specialization names
     */
    public function setSpecializations(array $names)
    {
        $this->clear();
        foreach ($names as $name) {
            $this->add(new AttributeSpecialization($name));
        }
    }
}
