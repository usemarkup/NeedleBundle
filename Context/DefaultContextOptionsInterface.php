<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;

interface DefaultContextOptionsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSetDecoratorForFacet(AttributeInterface $facet): ?FacetSetDecoratorInterface;
}
