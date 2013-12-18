<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FilterNonUnionValuesFacetSetDecorator;
use Mockery as m;

/**
 * Test for a facet set decorator that filters out non union values if union/combined values are present.
 */
class FilterNonUnionValuesFacetSetDecoratorTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testIsFacetSet()
    {
        $decorator = new FilterNonUnionValuesFacetSetDecorator();
        $this->assertInstanceOf('Markup\NeedleBundle\Facet\FacetSetInterface', $decorator);
    }

    //MORE TESTS....
}
