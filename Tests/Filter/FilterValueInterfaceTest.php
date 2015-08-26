<?php

namespace Markup\NeedleBundle\Tests\Filter;

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
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Filter\FilterValueInterface';
    }
}
