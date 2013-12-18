<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Context\SearchContext;

/**
* A test for a simple search context implementation.
*/
class SearchContextTest extends \PHPUnit_Framework_TestCase
{
    public function testSetItemsPerPageInConstructor()
    {
        $max = 42;
        $context = new SearchContext($max);
        $this->assertEquals($max, $context->getItemsPerPage());
    }

    public function testIsSearchContext()
    {
        $context = new SearchContext(7);
        $this->assertTrue($context instanceof \Markup\NeedleBundle\Context\SearchContextInterface);
    }

    public function testFacetsDoNotIgnoreCurrentFilters()
    {
        $context = new SearchContext(42);
        $facet = $this->getMock('Markup\NeedleBundle\Facet\FacetInterface');
        $this->assertFalse($context->getWhetherFacetIgnoresCurrentFilters($facet));
    }

    public function testGetInterceptorReturnsInterceptor()
    {
        $context = new SearchContext(42);
        $this->assertInstanceOf('Markup\NeedleBundle\Intercept\InterceptorInterface', $context->getInterceptor());
    }
}
