<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider;

class ConfiguredContextProvider
{
    /**
     * @var AttributeProviderInterface
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
     * @var \SplQueue<ContextDecoratorInterface>
     */
    private $decorators;

    /**
     * @param AttributeProviderInterface $filterProvider
     * @param FacetProviderInterface $facetProvider
     * @param FacetSetDecoratorProviderInterface $facetSetDecoratorProvider
     * @param CollatorProviderInterface $facetCollatorProvider
     * @param SortOrderProviderInterface $facetSortOrderProvider
     * @param ConfiguredInterceptorProvider $interceptorProvider
     */
    public function __construct(
        AttributeProviderInterface $filterProvider,
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
        $this->decorators = new \SplQueue();
    }

    /**
     * @param ContextConfigurationInterface $config
     * @return SearchContextInterface
     */
    public function createConfiguredContext(ContextConfigurationInterface $config)
    {
        $context = new ConfiguredContext(
            $config,
            $this->filterProvider,
            $this->facetProvider,
            $this->facetSetDecoratorProvider,
            $this->facetCollatorProvider,
            $this->facetSortOrderProvider,
            $this->interceptorProvider
        );
        foreach ($this->decorators as $decorator) {
            $context = $decorator->decorateContext($context);
        }

        return $context;
    }

    /**
     * Add a context decorator to be apply to any generated context. First decorators provided are applied first.
     *
     * @param ContextDecoratorInterface $decorator
     * @return self
     */
    public function addDecorator(ContextDecoratorInterface $decorator)
    {
        $this->decorators->enqueue($decorator);

        return $this;
    }
} 
