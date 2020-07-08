<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Collator\NullCollatorProvider;
use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\NullFacetSetDecoratorProvider;

final class NoopDefaultContext implements DefaultContextInterface
{
    public function getItemsPerPage(): ?int
    {
        return null;
    }

    public function getDefaultFacets(): array
    {
        return [];
    }

    public function getDefaultFilterQueries(): array
    {
        return [];
    }

    public function getDefaultSorts(): array
    {
        return [];
    }

    public function shouldRequestFacetValueForMissing(): bool
    {
        return false;
    }

    public function shouldUseFuzzyMatching(): bool
    {
        return false;
    }

    public function getFacetSetDecoratorProvider(): FacetSetDecoratorProviderInterface
    {
        return new NullFacetSetDecoratorProvider();
    }

    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet): bool
    {
        return true;
    }

    public function getFacetCollatorProvider(): CollatorProviderInterface
    {
        return new NullCollatorProvider();
    }

    public function getBoostQueryFields(): array
    {
        return [];
    }
}
