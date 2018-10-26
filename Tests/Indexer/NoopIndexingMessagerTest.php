<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\IndexingMessagerInterface;
use Markup\NeedleBundle\Indexer\NoopIndexingMessager;
use PHPUnit\Framework\TestCase;

class NoopIndexingMessagerTest extends TestCase
{
    public function testIsIndexingMessager()
    {
        $this->assertInstanceOf(IndexingMessagerInterface::class, new NoopIndexingMessager());
    }
}
