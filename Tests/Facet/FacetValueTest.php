<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Facet\FacetValue;

/**
* A test for a facet value implementation.
*/
class FacetValueTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFacetValue()
    {
        $facetValue = new \ReflectionClass('Markup\NeedleBundle\Facet\FacetValue');
        $this->assertTrue($facetValue->implementsInterface('Markup\NeedleBundle\Facet\FacetValueInterface'));
    }

    public function testGetValue()
    {
        $value = 'red';
        $facetValue = new FacetValue($value, 7);
        $this->assertEquals($value, $facetValue->getValue());
    }

    public function testGetDisplayValue()
    {
        $value = 'red';
        $facetValue = new FacetValue($value, 7);
        $this->assertEquals($value, $facetValue->getDisplayValue());
    }

    public function testCount()
    {
        $count = 7;
        $facetValue = new FacetValue('red', $count);
        $this->assertEquals($count, count($facetValue));
    }

    public function testCastToString()
    {
        $value = 'red';
        $facetValue = new FacetValue($value, 7);
        $this->assertEquals($value, (string) $facetValue);
    }
}
