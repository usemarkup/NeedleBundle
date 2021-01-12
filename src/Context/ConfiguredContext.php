<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider;

/**
 * A configuration that can be used to compose a SearchContext (optionally)
 */
class ConfiguredContext implements DefaultContextInterface
{
    /**
     * @var ContextConfigurationInterface
     */
    private $config;

    /**
     * @var FacetSetDecoratorProviderInterface
     */
    private $facetSetDecoratorProvider;

    /**
     * @var CollatorProviderInterface
     */
    private $facetCollatorProvider;

    /**
     * @var SortOrderProviderInterface
     */
    private $facetSortOrderProvider;

    /**
     * @var ConfiguredInterceptorProvider
     */
    private $interceptorProvider;

    public function __construct(
        ContextConfigurationInterface $config,
        FacetSetDecoratorProviderInterface $facetSetDecoratorProvider,
        CollatorProviderInterface $facetCollatorProvider,
        SortOrderProviderInterface $facetSortOrderProvider,
        ConfiguredInterceptorProvider $interceptorProvider
    ) {
        $this->config = $config;
        $this->facetSetDecoratorProvider = $facetSetDecoratorProvider;
        $this->facetCollatorProvider = $facetCollatorProvider;
        $this->facetSortOrderProvider = $facetSortOrderProvider;
        $this->interceptorProvider = $interceptorProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage(): ?int
    {
        return $this->config->getDefaultItemsPerPage() ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFacets(): array
    {
        $facets = [];
        foreach ($this->config->getDefaultFacetingAttributes() as $facetName) {
            $facets[] = $facetName;
        }

        return $facets;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFilterQueries(): array
    {
        return $this->config->getDefaultFilterQueries();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultSorts(): array
    {
        return $this->config->getDefaultSortsForNonSearchTermQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetSetDecoratorProvider(): FacetSetDecoratorProviderInterface
    {
        return $this->facetSetDecoratorProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet): bool
    {
        return $this->config->shouldIgnoreCurrentFilteredAttributesInFaceting();
    }

    /**
     * {@inheritdoc}
     */
    public function getBoostQueryFields(): array
    {
        return $this->config->getDefaultBoosts();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetCollatorProvider(): CollatorProviderInterface
    {
        return $this->facetCollatorProvider;
    }

    /**
     * Gets a signifier for the sort order to use for sorting of facet values within the search engine itself (i.e. not within the web application).
     *
     * @return \Markup\NeedleBundle\Facet\SortOrderProviderInterface
     **/
    public function getFacetSortOrderProvider()
    {
        return $this->facetSortOrderProvider;
    }

    /**
     * Gets an interceptor object that can intercept a lookup on a backend and provide redirects to specific places.
     *
     * @return \Markup\NeedleBundle\Intercept\InterceptorInterface
     **/
    public function getInterceptor()
    {
        return $this->interceptorProvider->createInterceptor($this->config->getIntercepts());
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRequestFacetValueForMissing(): bool
    {
        return false;
    }

    public function shouldUseFuzzyMatching(): bool
    {
        return $this->config->shouldUseFuzzyMatching();
    }
}
