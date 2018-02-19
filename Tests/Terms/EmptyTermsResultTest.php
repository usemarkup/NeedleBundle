<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Terms\EmptyTermsResult;
use Markup\NeedleBundle\Terms\TermsResultInterface;
use PHPUnit\Framework\TestCase;

class EmptyTermsResultTest extends TestCase
{
    public function testIsTermsResult()
    {
        $this->assertInstanceOf(TermsResultInterface::class, new EmptyTermsResult());
    }
}
