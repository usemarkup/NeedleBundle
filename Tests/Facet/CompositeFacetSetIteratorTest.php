<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\CompositeFacetSetIterator;
use Markup\NeedleBundle\Facet\FacetSetArrayIterator;
use Markup\NeedleBundle\Facet\FacetSetInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for an iterator that can go over a composite of arbitrary facets and emit individual facet sets.
*/
class CompositeFacetSetIteratorTest extends TestCase
{
    /**
     * @dataProvider fixtures
     **/
    public function testIteration($facetValues, $valueDelimiter, $facetCount, $valueSets)
    {
        $values = [];
        foreach ($facetValues as $facetValueValue => $facetValueCount) {
            $values[] = new \Markup\NeedleBundle\Facet\FacetValue($facetValueValue, $facetValueCount);
        }
        $facetSet = $this->createMock(FacetSetInterface::class);
        $facetSet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new FacetSetArrayIterator($values)));
        $it = new CompositeFacetSetIterator($facetSet, $valueDelimiter);
        $emittedFacetSets = iterator_to_array($it);
        $this->assertCount($facetCount, $emittedFacetSets);
        $this->assertContainsOnly(FacetSetInterface::class, $emittedFacetSets);
        $emittedValueSets = [];
        foreach ($emittedFacetSets as $facetSet) {
            $valueSet = [];
            foreach ($facetSet as $facetValue) {
                $valueSet[$facetValue->getValue()] = count($facetValue);
            }
            $emittedValueSets[] = $valueSet;
        }
        $this->assertEquals($valueSets, $emittedValueSets);
    }

    public function fixtures()
    {
        return [
            [
                [
                    'fit::tight' => 2,
                    'fit::loose' => 3,
                    'sleeve_length::long' => 10,
                ],
                '::',
                2,
                [
                    [
                        'tight' => 2,
                        'loose' => 3,
                    ],
                    [
                        'long' => 10,
                    ],
                ],
            ],
        ];
    }
}
