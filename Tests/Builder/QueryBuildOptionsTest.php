<?php

namespace Markup\NeedleBundle\Tests\Builder;

use Markup\NeedleBundle\Builder\QueryBuildOptions;
use PHPUnit\Framework\TestCase;

class QueryBuildOptionsTest extends TestCase
{
    public function testUsesWildCardSearchWhenChosen()
    {
        $options = new QueryBuildOptions(['useWildcardSearch' => true]);
        $this->assertTrue($options->useWildcardSearch());
    }

    public function testUsesWildcardSearchWhenNotChosen()
    {
        $options = new QueryBuildOptions();
        $this->assertFalse($options->useWildcardSearch());
    }
}
