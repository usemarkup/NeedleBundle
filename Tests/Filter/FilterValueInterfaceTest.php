<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a filter value interface.
*/
class FilterValueInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'getSearchValue',
            'getSlug',
            'getValueType',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return FilterValueInterface::class;
    }
}
