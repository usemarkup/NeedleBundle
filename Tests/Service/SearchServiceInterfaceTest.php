<?php

namespace Markup\NeedleBundle\Tests\Service;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
 * A test for a search service interface.
 */
class SearchServiceInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'executeQuery',
            'setContext',
            'addDecorator',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Service\SearchServiceInterface';
    }
}
