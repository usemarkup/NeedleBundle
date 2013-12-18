<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a facet set interface
*/
class FacetSetInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return array(
            'count',
            'getIterator',
            'getFacet',
            );
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Facet\FacetSetInterface';
    }

    public function testIsTraversable()
    {
        $set = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($set->implementsInterface('Traversable'));
    }

    public function testIsCountable()
    {
        $set = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($set->implementsInterface('Countable'));
    }
}
