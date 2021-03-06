<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\UnionFilterValueInterface;
use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for the interface of a filter value representing a union of values.
*/
class UnionFilterValueInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'getSearchValue',
            'getSlug',
            'getValueType',
            'getValues',
            'getIterator',
            'addFilterValue',
            'count',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return UnionFilterValueInterface::class;
    }

    public function testIsFilterValue()
    {
        $unionValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($unionValue->implementsInterface(FilterValueInterface::class));
    }

    public function testIsIteratorAggregate()
    {
        $unionValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($unionValue->implementsInterface(\IteratorAggregate::class));
    }
}
