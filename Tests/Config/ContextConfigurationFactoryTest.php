<?php

namespace Markup\NeedleBundle\Tests\Config;

use Markup\NeedleBundle\Config\ContextConfigurationFactory;

class ContextConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $configHash = ['items_per_page' => 42];
        $fac = new ContextConfigurationFactory();
        $config = $fac->createConfigFromHash($configHash);
        $this->assertInstanceOf('Markup\NeedleBundle\Config\ContextConfigurationInterface', $config);
        $this->assertEquals(42, $config->getDefaultItemsPerPage());
    }
}
