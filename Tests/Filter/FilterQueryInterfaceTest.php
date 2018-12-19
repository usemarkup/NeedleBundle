<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a filter query interface.
*/
class FilterQueryInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'getSearchKey',
            'getSearchValue',
            'getFilter',
            'getFilterValue',
            'getValueType',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return FilterQueryInterface::class;
    }
}
