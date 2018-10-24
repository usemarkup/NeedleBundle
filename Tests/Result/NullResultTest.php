<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Result\NullResult;
use Markup\NeedleBundle\Result\ResultInterface;
use PHPUnit\Framework\TestCase;

class NullResultTest extends TestCase
{
    public function testIsResult()
    {
        $this->assertInstanceOf(ResultInterface::class, new NullResult());
    }
}
