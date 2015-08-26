<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a facet value decorator interface.
*/
class FacetValueDecoratorInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'getValue',
            'getDisplayValue',
            'count',
            '__toString',
            'decorate',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Facet\FacetValueDecoratorInterface';
    }

    public function testIsFacetValue()
    {
        $facetValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($facetValue->implementsInterface('Markup\NeedleBundle\Facet\FacetValueInterface'));
    }
}
