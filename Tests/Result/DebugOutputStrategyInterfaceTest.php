<?php

namespace Markup\NeedleBundle\Tests\Debug;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for an interface for a debug output strategy.
*/
class DebugOutputStrategyInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return array(
            'hasDebugOutput',
            'getDebugOutput',
            );
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Result\DebugOutputStrategyInterface';
    }
}
