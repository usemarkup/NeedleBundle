<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Filter\FilterProviderInterface;
use Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider;

class ConfiguredContextProvider
{
    /**
     * @var FilterProviderInterface
     */
    private $filterProvider;

    /**
     * @var FacetProviderInterface
     */
    private $facetProvider;

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

    /**
     * @param FilterProviderInterface $filterProvider
     * @param FacetProviderInterface $facetProvider
     * @param FacetSetDecoratorProviderInterface $facetSetDecoratorProvider
     * @param CollatorProviderInterface $facetCollatorProvider
     * @param SortOrderProviderInterface $facetSortOrderProvider
     * @param ConfiguredInterceptorProvider $interceptorProvider
     */
    public function __construct(
        FilterProviderInterface $filterProvider,
        FacetProviderInterface $facetProvider,
        FacetSetDecoratorProviderInterface $facetSetDecoratorProvider,
        CollatorProviderInterface $facetCollatorProvider,
        SortOrderProviderInterface $facetSortOrderProvider,
        ConfiguredInterceptorProvider $interceptorProvider
    ) {
        $this->filterProvider = $filterProvider;
        $this->facetProvider = $facetProvider;
        $this->facetSetDecoratorProvider = $facetSetDecoratorProvider;
        $this->facetCollatorProvider = $facetCollatorProvider;
        $this->facetSortOrderProvider = $facetSortOrderProvider;
        $this->interceptorProvider = $interceptorProvider;
    }

    /**
     * @param ContextConfigurationInterface $config
     * @return SearchContextInterface
     */
    public function createConfiguredContext(ContextConfigurationInterface $config)
    {
        return new ConfiguredContext(
            $config,
            $this->filterProvider,
            $this->facetProvider,
            $this->facetSetDecoratorProvider,
            $this->facetCollatorProvider,
            $this->facetSortOrderProvider,
            $this->interceptorProvider
        );
    }
} 
