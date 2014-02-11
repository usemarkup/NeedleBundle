<?php

namespace Markup\NeedleBundle\Facet;

class FacetSetDecoratorProvider implements FacetSetDecoratorProviderInterface
{
    /**
     * @var FacetSetDecoratorInterface[]
     */
    private $decorators;

    public function __construct()
    {
        $this->decorators = array();
    }

    /**
     * Gets a facet set decorator for a provided facet, or returns null.
     *
     * @param FacetInterface $facet
     * @return FacetSetDecoratorInterface
     */
    public function getDecoratorForFacet(FacetInterface $facet)
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
