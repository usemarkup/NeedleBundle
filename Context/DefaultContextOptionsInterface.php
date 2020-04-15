<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;

interface DefaultContextOptionsInterface
{
    public function getSetDecoratorForFacet(AttributeInterface $facet): ?FacetSetDecoratorInterface;

    public function getFacetCollatorProvider(): CollatorProviderInterface;
}
