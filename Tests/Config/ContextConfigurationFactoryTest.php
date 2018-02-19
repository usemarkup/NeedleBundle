<?php

namespace Markup\NeedleBundle\Tests\Config;

use Markup\NeedleBundle\Config\ContextConfigurationFactory;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use PHPUnit\Framework\TestCase;

class ContextConfigurationFactoryTest extends TestCase
{
    public function testCreate()
    {
        $configHash = ['items_per_page' => 42];
        $fac = new ContextConfigurationFactory();
        $config = $fac->createConfigFromHash($configHash);
        $this->assertInstanceOf(ContextConfigurationInterface::class, $config);
        $this->assertEquals(42, $config->getDefaultItemsPerPage());
    }
}
