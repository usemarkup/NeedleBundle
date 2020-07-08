<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Attribute;

/**
 * If true the facet value will be 'Canonicalize' for display
 */
interface AttributeValueOptionsInterface
{
    public function shouldCanonicalizeDisplayValue(): bool;
}
