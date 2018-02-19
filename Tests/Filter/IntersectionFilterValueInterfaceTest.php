<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\IntersectionFilterValueInterface;
use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for the interface of a filter value representing an intersection of values.
*/
class IntersectionFilterValueInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'getSearchValue',
            'getSlug',
            'getValues',
            'getIterator',
            'addFilterValue',
            'count',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return IntersectionFilterValueInterface::class;
    }

    public function testIsFilterValue()
    {
        $intersectionValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($intersectionValue->implementsInterface(FilterValueInterface::class));
    }

    public function testIsIteratorAggregate()
    {
        $intersectionValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($intersectionValue->implementsInterface('IteratorAggregate'));
    }
}
