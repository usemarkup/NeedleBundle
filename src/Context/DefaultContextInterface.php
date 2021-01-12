<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;

/**
 * A default that can be used to compose a SelectQuery or SearchContext
 */
interface DefaultContextInterface
{
    public function getItemsPerPage(): ?int;

    public function getDefaultFacets(): array;

    public function getDefaultFilterQueries(): array;

    public function getDefaultSorts(): array;

    public function getBoostQueryFields(): array;

    public function shouldRequestFacetValueForMissing(): bool;

    public function shouldUseFuzzyMatching(): bool;

    public function getFacetSetDecoratorProvider(): FacetSetDecoratorProviderInterface;

    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet): bool;

    public function getFacetCollatorProvider(): CollatorProviderInterface;
}
