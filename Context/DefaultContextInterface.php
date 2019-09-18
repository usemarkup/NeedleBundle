<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

/**
 * A default that can be used to compose a SelectQuery or SearchContext
 */
interface DefaultContextInterface
{
    public function getItemsPerPage(): ?int;

    public function getFacets(): array;

    public function getDefaultFilterQueries(): array;

    public function getDefaultSorts(bool $isSearchTermQuery): array;

    public function shouldRequestFacetValueForMissing(): bool;

    public function shouldUseFuzzyMatching(): bool;
}
