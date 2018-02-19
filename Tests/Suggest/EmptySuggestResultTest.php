<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\EmptySuggestResult;
use Markup\NeedleBundle\Suggest\SuggestResultInterface;
use PHPUnit\Framework\TestCase;

class EmptySuggestResultTest extends TestCase
{
    public function testIsSuggestResult()
    {
        $this->assertInstanceOf(SuggestResultInterface::class, new EmptySuggestResult());
    }
}
