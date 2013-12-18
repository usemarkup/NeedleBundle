<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for the interface of a filter value representing an intersection of values.
*/
class IntersectionFilterValueInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return array(
            'getSearchValue',
            'getSlug',
            'getValues',
            'getIterator',
            'addFilterValue',
            'count',
            );
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Filter\IntersectionFilterValueInterface';
    }

    public function testIsFilterValue()
    {
        $intersectionValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($intersectionValue->implementsInterface('Markup\NeedleBundle\Filter\FilterValueInterface'));
    }

    public function testIsIteratorAggregate()
    {
        $intersectionValue = new \ReflectionClass($this->getInterfaceUnderTest());
        $this->assertTrue($intersectionValue->implementsInterface('IteratorAggregate'));
    }
}
