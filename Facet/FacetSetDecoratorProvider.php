<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

class FacetSetDecoratorProvider implements FacetSetDecoratorProviderInterface
{
    /**
     * @var FacetSetDecoratorInterface[]
     */
    private $decorators;

    public function __construct()
    {
        $this->decorators = [];
    }

    /**
     * Gets a facet set decorator for a provided facet, or returns null.
     *
     * @param AttributeInterface $facet
     * @return FacetSetDecoratorInterface|null
     */
    public function getDecoratorForFacet(AttributeInterface $facet)
    {
        if (!isset($this->decorators[$facet->getName()])) {
            return null;
        }

        return $this->decorators[$facet->getName()];
    }

    /**
     * @param string $field
     * @param FacetSetDecoratorInterface $decorator
     * @return self
     */
    public function addDecorator($field, FacetSetDecoratorInterface $decorator)
    {
        $this->decorators[$field] = $decorator;

        return $decorator;
    }
}
