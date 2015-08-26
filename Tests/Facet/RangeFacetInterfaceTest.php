<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a range facet interface, being a facet that represents a set of ranges.
*/
class RangeFacetInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'getName',
            'getDisplayName',
            'getSearchKey',
            '__toString',
            'getRangeSize',
            'getRangesStart',
            'getRangesEnd',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Facet\RangeFacetInterface';
    }

    public function testIsAttribute()
    {
        $rangeFacet = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($rangeFacet->implementsInterface('Markup\NeedleBundle\Attribute\AttributeInterface'));
    }
}
