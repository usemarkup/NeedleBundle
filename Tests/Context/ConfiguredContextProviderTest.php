<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Context\ConfiguredContextProvider;
use Mockery as m;

class ConfiguredContextProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->filterProvider = m::mock('Markup\NeedleBundle\Attribute\AttributeProviderInterface');
        $this->facetProvider = m::mock('Markup\NeedleBundle\Facet\FacetProviderInterface');
        $this->facetSetDecoratorProvider = m::mock('Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface');
        $this->facetCollatorProvider = m::mock('Markup\NeedleBundle\Collator\CollatorProviderInterface');
        $this->facetSortOrderProvider = m::mock('Markup\NeedleBundle\Facet\SortOrderProviderInterface');
        $this->interceptorProvider = m::mock('Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider');
        $this->provider = new ConfiguredContextProvider(
            $this->filterProvider,
            $this->facetProvider,
            $this->facetSetDecoratorProvider,
            $this->facetCollatorProvider,
            $this->facetSortOrderProvider,
            $this->interceptorProvider
        );
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateConfiguredContext()
    {
        $config = m::mock('Markup\NeedleBundle\Config\ContextConfigurationInterface');
        $this->assertInstanceOf('Markup\NeedleBundle\Context\ConfiguredContext', $this->provider->createConfiguredContext($config));
    }

    public function testUseContextDecoratorsWhenCreating()
    {
        $context = m::mock('Markup\NeedleBundle\Context\SearchContextInterface');
        $contextDecorator = m::mock('Markup\NeedleBundle\Context\ContextDecoratorInterface');
        $contextDecorator
            ->shouldReceive('decorateContext')
            ->andReturn($context);
        $this->provider->addDecorator($contextDecorator);
        $config = m::mock('Markup\NeedleBundle\Config\ContextConfigurationInterface');
        $this->assertSame($context, $this->provider->createConfiguredContext($config));
    }
}
