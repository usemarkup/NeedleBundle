<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a facet value interface.
*/
class FacetValueInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return array(
            'getValue',
            'getDisplayValue',
            'count',
            '__toString',
            );
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Facet\FacetValueInterface';
    }

    public function testIsCountable()
    {
        $facetValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($facetValue->implementsInterface('Countable'));
    }
}
