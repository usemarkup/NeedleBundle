<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Context\SearchContext;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Intercept\InterceptorInterface;
use Markup\NeedleBundle\Sort\EmptySortCollection;
use PHPUnit\Framework\TestCase;

/**
* A test for a simple search context implementation.
*/
class SearchContextTest extends TestCase
{
    public function testSetItemsPerPageInConstructor()
    {
        $max = 42;
        $context = new SearchContext($max, [], [], new EmptySortCollection());
        $this->assertEquals($max, $context->getItemsPerPage());
    }

    public function testIsSearchContext()
    {
        $context = new SearchContext(7, [], [], new EmptySortCollection());
        $this->assertInstanceOf(SearchContextInterface::class, $context);
    }

    public function testGetInterceptorReturnsInterceptor()
    {
        $context = new SearchContext(42, [], [], new EmptySortCollection());
        $this->assertInstanceOf(InterceptorInterface::class, $context->getInterceptor());
    }
}
