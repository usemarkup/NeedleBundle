<?php

namespace Markup\NeedleBundle\Tests\Filter;

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
            'getValues',
            'getIterator',
            'addFilterValue',
            'count',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Filter\UnionFilterValueInterface';
    }

    public function testIsFilterValue()
    {
        $unionValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($unionValue->implementsInterface('Markup\NeedleBundle\Filter\FilterValueInterface'));
    }

    public function testIsIteratorAggregate()
    {
        $unionValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($unionValue->implementsInterface('IteratorAggregate'));
    }
}
