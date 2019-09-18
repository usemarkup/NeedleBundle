<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

final class NoopDefaultContext implements DefaultContextInterface
{
    public function getItemsPerPage(): ?int
    {
        return null;
    }

    public function getFacets(): array
    {
        return [];
    }

    public function getDefaultFilterQueries(): array
    {
        return [];
    }

    public function getDefaultSorts(bool $isSearchTermQuery): array
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
}
