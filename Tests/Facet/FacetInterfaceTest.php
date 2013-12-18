<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a facet interface.
*/
class FacetInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return array(
            'getName',
            'getDisplayName',
            'getSearchKey',
            '__toString',
            );
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Facet\FacetInterface';
    }
}
