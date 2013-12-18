<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a composite facet set interface
*/
class CompositeFacetSetInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return array(
            'count',
            'getIterator',
            'getFacet',
            'getSubFacetSets',
            );
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Facet\CompositeFacetSetInterface';
    }

    public function testIsFacetSet()
    {
        $set = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($set->implementsInterface('Markup\NeedleBundle\Facet\FacetSetInterface'));
    }
}
