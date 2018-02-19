<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\Intercept;
use Markup\NeedleBundle\Intercept\InterceptInterface;
use PHPUnit\Framework\TestCase;

/**
* Test for a simple intercept class.
*/
class InterceptTest extends TestCase
{
    public function setUp()
    {
        $this->uri = 'uri';
        $this->intercept = new Intercept($this->uri);
    }

    public function testIsIntercept()
    {
        $this->assertInstanceOf(InterceptInterface::class, $this->intercept);
    }
}
