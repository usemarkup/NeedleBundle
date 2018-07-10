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
     * @var SpecializationContextGroupFilterInterface
     */
    private $contextFilter;

    public function __construct(
        AttributeSpecializationContextRegistryInterface $attributeSpecializationContextRegistry,
        SpecializationContextGroupFilterInterface $contextFilter
    ) {
        $this->attributeSpecializationContextRegistry = $attributeSpecializationContextRegistry;
        $this->contextFilter = $contextFilter;
    }

    /**
     * @param AttributeSpecializationInterface[] $specializations
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
     * @param AttributeSpecializationInterface[] $specializations
     * @return array
     */
    public function getAllValidAttributeSpecializationContextGroups($specializations): array
    {
        $contextCollections = [];
        foreach ($specializations as $specialization) {
            $contextCollections[$specialization->getName()] = $this->attributeSpecializationContextRegistry->getContexts($specialization);
        }

        $filter = function (array $contexts) {
            if (count($contexts) < 2) {
                return true;
            }

            return $this->contextFilter->accept(array_map(
                function (AttributeSpecializationContextInterface $context) {
                    return $context->getData();
                },
                $contexts
            ));
        };
        $product = $this->calculateCartesianProduct($contextCollections, $filter);

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

    private function calculateCartesianProduct(array $input, ?callable $filter = null)
    {
        // filter out empty values
        $input = array_filter($input);

        $result = [[]];

        foreach ($input as $key => $values) {
            $append = [];

            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    if (null !== $filter && !$filter($product)) {
                        continue;
                    }
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }
}
