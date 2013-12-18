<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a filter interface.
*/
class FilterInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return array(
            'getName',
            'getDisplayName',
            'getSearchKey',
            );
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Filter\FilterInterface';
    }
}
