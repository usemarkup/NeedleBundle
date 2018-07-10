<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Attribute;

/**
 * Composite implementation of a filter for groups of specialization contexts (to enforce valid/acceptable combinations).
 */
class CompositeSpecializationContextGroupFilter implements SpecializationContextGroupFilterInterface
{
    /**
     * @var SpecializationContextGroupFilterInterface[]
     */
    private $filters;

    public function __construct()
    {
        $this->filters = [];
    }

    /**
     * @param array $attributeData Some data keyed by attribute name, the values being mixed (can depend on the attribute).
     * @return bool
     */
    public function accept(array $attributeData): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter->accept($attributeData)) {
                return false;
            }
        }

        return true;
    }

    public function addFilter(SpecializationContextGroupFilterInterface $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }
}
