<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Result\PropertyIterator;

/**
* A test for an iterator that can take a search result and emit a certain property of each result document.
*/
class PropertyIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testEmitProperty()
    {
        $document = new \stdClass();
        $property = 'prop';
        $value = 'this is a value';
        $document->$property = $value;
        $resultIterator = new \ArrayIterator([$document, $document, $document]);
        $result = $this->createMock('IteratorAggregate');
        $result
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($resultIterator));
        $propertyIterator = new PropertyIterator($result, $property);
        $emissions = iterator_to_array($propertyIterator);
        $emission = $emissions[0];
        $this->assertEquals($value, $emission);
    }
}
