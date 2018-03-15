<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * Provides AttributeSpecializationContextGroups for 'all contexts' or the 'current context'
 */
class AttributeSpecializationContextGroupRegistry
{
    /**
     * @var AttributeSpecializationContextRegistryInterface
     */
    private $attributeSpecializationContextRegistry;

    /**
     * @param array AttributeSpecializationContextRegistryInterface
     */
    public function __construct(AttributeSpecializationContextRegistryInterface $attributeSpecializationContextRegistry)
    {
        $this->attributeSpecializationContextRegistry = $attributeSpecializationContextRegistry;
    }

    /**
     * @param AttributeSpecialization[] $specializations
     * @return array
     */
    public function getAllAttributeSpecializationContextGroups($specializations): array
    {
        $contextCollections = [];
        foreach ($specializations as $specialization) {
            $contextCollections[$specialization->getName()] = $this->attributeSpecializationContextRegistry->getContexts($specialization);
        }

        $product = $this->calculateCartesianProduct($contextCollections);

        return array_map(function ($s) {
            return new AttributeSpecializationContextGroup($s);
        }, $product);
    }

    /**
     * @param AttributeSpecialization[] $specializations
     * @return AttributeSpecializationContextGroup
     */
    public function getCurrentAttributeSpecializationContextGroup($specializations): AttributeSpecializationContextGroup
    {
        $contexts = [];
        foreach ($specializations as $specialization) {
            $contexts[$specialization->getName()] = $this->attributeSpecializationContextRegistry->getContext($specialization);
        }

        return new AttributeSpecializationContextGroup($contexts);
    }

    private function calculateCartesianProduct(array $input)
    {
        // filter out empty values
        $input = array_filter($input);

        $result = [[]];

        foreach ($input as $key => $values) {
            $append = [];

            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }
}
