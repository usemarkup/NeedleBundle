<?php

namespace Markup\NeedleBundle\Facet;

use PHPUnit\Framework\TestCase;

/**
* A test for a facet value implementation.
*/
class FacetValueTest extends TestCase
{
    public function testIsFacetValue()
    {
        $facetValue = new \ReflectionClass(FacetValue::class);
        $this->assertTrue($facetValue->implementsInterface(FacetValueInterface::class));
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

    public function testGetDisplayNameUsingDisplayStrategy()
    {
        $displayStrategy = function ($value) {
            return strtoupper($value);
        };
        $value = 'value';
        $facetValue = new FacetValue($value, 7, $displayStrategy);
        $this->assertEquals('VALUE', $facetValue->getDisplayValue());
    }
}
