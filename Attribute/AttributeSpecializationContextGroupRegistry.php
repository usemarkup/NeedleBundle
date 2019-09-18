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
            if (!$specialization instanceof AttributeSpecializationInterface) {
                throw new \InvalidArgumentException();
            }

            $specializationName = $specialization->getName();

            $contextCollections[$specializationName] = $this->attributeSpecializationContextRegistry->getContexts(
                $specializationName
            );
        }

        $product = $this->calculateCartesianProduct($contextCollections);

        return array_map(
            function ($s) {
                return new AttributeSpecializationContextGroup($s);
            },
            $product
        );
    }

    /**
     * @param string[] $specializations
     * @return array
     */
    public function getAllValidAttributeSpecializationContextGroups($specializations): array
    {
        $contextCollections = [];
        foreach ($specializations as $specializationName) {
            if (!is_string($specializationName)) {
                throw new \InvalidArgumentException();
            }

            $contextCollections[$specializationName] = $this->attributeSpecializationContextRegistry->getContexts(
                $specializationName
            );
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

    public function createAttributeSpecializationContextGroup(
        array $specializations,
        SpecializationContextHashInterface $contextHash
    ): AttributeSpecializationContextGroup {
        $contexts = [];

        foreach ($specializations as $specialization) {
            if (!$specialization instanceof AttributeSpecializationInterface) {
                continue;
            }

            $contextSpecializationName = $specialization->getName();

            if (!$contextHash->hasContext($contextSpecializationName)) {
                throw new \Exception(
                    sprintf(
                        'Unable to find context to specialize attribute "%s" for "%s"',
                        $contextSpecializationName,
                        $contextSpecializationName
                    )
                );
            }

            $context = $this->attributeSpecializationContextRegistry->getContextForData(
                $contextSpecializationName,
                $contextHash->getContextData($contextSpecializationName)
            );

            $contexts[$specialization->getName()] = $context;
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
