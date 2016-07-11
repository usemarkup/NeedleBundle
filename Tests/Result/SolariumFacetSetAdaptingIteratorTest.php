<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Collator\CollatorInterface;
use Markup\NeedleBundle\Facet\FacetSetIteratorInterface;
use Markup\NeedleBundle\Result\SolariumFacetSetAdaptingIterator;
use Mockery as m;

/**
* A test for an iterator that can wrap a Solarium facet set and emit generic facet values.
*/
class SolariumFacetSetAdaptingIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFacetSetIterator()
    {
        $refl = new \ReflectionClass(SolariumFacetSetAdaptingIterator::class);
        $this->assertTrue($refl->implementsInterface(FacetSetIteratorInterface::class));
    }

    public function testIterate()
    {
        $value = 'red';
        $count = 5;
        $solariumFacetField = $this->getMockBuilder('Solarium\QueryType\Select\Result\Facet\Field')
            ->disableOriginalConstructor()
            ->getMock();
        $valuesIterator = new \ArrayIterator([$value => $count]);
        $solariumFacetField
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($valuesIterator));
        $it = new SolariumFacetSetAdaptingIterator($solariumFacetField);
        $values = iterator_to_array($it);
        $this->assertCount(1, $values, 'checking there is one value');
        $this->assertContainsOnly('Markup\NeedleBundle\Facet\FacetValueInterface', $values);
        foreach ($values as $singleValue) {
            break;
        }
        $this->assertEquals($value, $singleValue->getValue());
        $this->assertCount($count, $singleValue);
    }

    public function testIteratorWithCollation()
    {
        $values = [
            'red'               => 1,
            'blue'              => 2,
            'yellow'            => 3,
        ];
        $expectedValues = array_reverse($values);
        $collator = m::mock(CollatorInterface::class);
        $collator
            ->shouldReceive('compare')
            ->andReturnUsing(
                function ($color1, $color2) {
                    return strlen($color2) - strlen($color1);
                }
            );
        $it = new SolariumFacetSetAdaptingIterator($values, $collator);
        $this->assertEquals(array_keys($expectedValues), array_keys(iterator_to_array($it)));
    }

    public function testCount()
    {
        $values = [
            'red'               => 1,
            'blue'              => 2,
            'yellow'            => 3,
        ];
        $it = new SolariumFacetSetAdaptingIterator($values);
        $this->assertCount(3, $it);
    }

    protected function tearDown()
    {
        m::close();
    }
}
