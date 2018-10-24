<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Service;

use Markup\NeedleBundle\Service\NoopSearchService;
use Markup\NeedleBundle\Service\SearchServiceInterface;
use PHPUnit\Framework\TestCase;

class NoopSearchServiceTest extends TestCase
{
    public function testIsSearchService()
    {
        $this->assertInstanceOf(SearchServiceInterface::class, new NoopSearchService());
    }
}
