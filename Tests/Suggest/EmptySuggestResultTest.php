<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\EmptySuggestResult;

class EmptySuggestResultTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSuggestResult()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Suggest\SuggestResultInterface', new EmptySuggestResult());
    }
}
