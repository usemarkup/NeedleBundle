<?php

namespace Markup\NeedleBundle\Facet;

/**
* A facet set decorator that composes other decorators and applies them in order.
*/
class CompositeFacetSetDecorator extends FacetSetDecorator
{
    /**
     * A collection of facet set decorators.
     *
     * @var FacetSetDecoratorInterface[]
     **/
    private $decorators;

    /**
     * @param FacetSetDecoratorInterface[] $decorators An array of facet set decorators.
     **/
    public function __construct(array $decorators)
    {
        $this->decorators = $decorators;
    }

    public function decorate(FacetSetInterface $facetSet)
    {
        if (count($this->decorators) == 0) {
            return parent::decorate($facetSet);
        }
        $decorators = array_values($this->decorators);
        $decorator = null;
        foreach ($decorators as $index => $decorator) {
            if ($index === 0) {
                $decorator->decorate($facetSet);
                continue;
            }
            $decorator->decorate($decorators[$index - 1]);
        }
        parent::decorate($decorator);

        return $this;
    }
}
