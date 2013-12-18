<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\CompositeFacetSetIterator;
use Markup\NeedleBundle\Facet\FacetSetArrayIterator;

/**
* A test for an iterator that can go over a composite of arbitrary facets and emit individual facet sets.
*/
class CompositeFacetSetIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider fixtures
     **/
    public function testIteration($facetValues, $valueDelimiter, $facetCount, $valueSets)
    {
        $values = array();
        foreach ($facetValues as $facetValueValue => $facetValueCount) {
            $values[] = new \Markup\NeedleBundle\Facet\FacetValue($facetValueValue, $facetValueCount);
        }
        $facetSet = $this->getMock('Markup\NeedleBundle\Facet\FacetSetInterface');
        $facetSet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new FacetSetArrayIterator($values)));
        $it = new CompositeFacetSetIterator($facetSet, $valueDelimiter);
        $emittedFacetSets = iterator_to_array($it);
        $this->assertCount($facetCount, $emittedFacetSets);
        $this->assertContainsOnly('Markup\NeedleBundle\Facet\FacetSetInterface', $emittedFacetSets);
        $emittedValueSets = array();
        foreach ($emittedFacetSets as $facetSet) {
            $valueSet = array();
            foreach ($facetSet as $facetValue) {
                $valueSet[$facetValue->getValue()] = count($facetValue);
            }
            $emittedValueSets[] = $valueSet;
        }
        $this->assertEquals($valueSets, $emittedValueSets);
    }

    public function fixtures()
    {
        return array(
            array(
                array(
                    'fit::tight' => 2,
                    'fit::loose' => 3,
                    'sleeve_length::long' => 10,
                    ),
                '::',
                2,
                array(
                    array(
                        'tight' => 2,
                        'loose' => 3,
                        ),
                    array(
                        'long' => 10,
                        ),
                    ),
                ),
            );
    }
}
