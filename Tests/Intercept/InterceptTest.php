<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\Intercept;

/**
* Test for a simple intercept class.
*/
class InterceptTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->uri = 'uri';
        $this->intercept = new Intercept($this->uri);
    }

    public function testIsIntercept()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Intercept\InterceptInterface', $this->intercept);
    }
}
