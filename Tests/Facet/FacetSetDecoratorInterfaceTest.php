<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;
use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a facet set interface
*/
class FacetSetDecoratorInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'count',
            'decorate',
            'getIterator',
            'getFacet',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return FacetSetDecoratorInterface::class;
    }

    public function testIsFacetSet()
    {
        $set = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($set->implementsInterface('Markup\NeedleBundle\Facet\FacetSetInterface'));
    }
}
