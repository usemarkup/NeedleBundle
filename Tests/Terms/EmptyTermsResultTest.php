<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Terms\EmptyTermsResult;

class EmptyTermsResultTest extends \PHPUnit_Framework_TestCase
{
    public function testIsTermsResult()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Terms\TermsResultInterface', new EmptyTermsResult());
    }
}
