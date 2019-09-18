<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Context\ConfiguredContext;
use Markup\NeedleBundle\Context\ConfiguredContextProvider;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ConfiguredContextProviderTest extends MockeryTestCase
{
    protected function setUp()
    {
        $this->filterProvider = m::mock(AttributeProviderInterface::class);
        $this->facetSetDecoratorProvider = m::mock(FacetSetDecoratorProviderInterface::class);
        $this->facetCollatorProvider = m::mock(CollatorProviderInterface::class);
        $this->facetSortOrderProvider = m::mock(SortOrderProviderInterface::class);
        $this->interceptorProvider = m::mock(ConfiguredInterceptorProvider::class);
        $this->provider = new ConfiguredContextProvider(
            $this->filterProvider,
            $this->facetSetDecoratorProvider,
            $this->facetCollatorProvider,
            $this->facetSortOrderProvider,
            $this->interceptorProvider
        );
    }

    public function testCreateConfiguredContext()
    {
        $config = m::mock(ContextConfigurationInterface::class);
        $this->assertInstanceOf(ConfiguredContext::class, $this->provider->createConfiguredContext($config));
    }
}
