<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetSetInterface;
use Markup\NeedleBundle\Facet\FilterNonUnionValuesFacetSetDecorator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Test for a facet set decorator that filters out non union values if union/combined values are present.
 */
class FilterNonUnionValuesFacetSetDecoratorTest extends MockeryTestCase
{
    public function testIsFacetSet()
    {
        $decorator = new FilterNonUnionValuesFacetSetDecorator();
        $this->assertInstanceOf(FacetSetInterface::class, $decorator);
    }
}
