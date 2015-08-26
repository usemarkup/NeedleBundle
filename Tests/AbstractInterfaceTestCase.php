<?php

namespace Markup\NeedleBundle\Tests;

/**
* An abstract test case class for tests that check the expected public methods of interfaces (to prevent regressions).
*/
abstract class AbstractInterfaceTestCase extends \PHPUnit_Framework_TestCase
{
    public function testExpectedPublicMethods()
    {
        $expectedPublicMethods = $this->getExpectedPublicMethods();
        $interface = new \ReflectionClass($this->getInterfaceUnderTest());
        $actualPublicMethods = [];
        foreach ($interface->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $actualPublicMethods[] = $method->name;
        }
        sort($expectedPublicMethods);
        sort($actualPublicMethods);
        $this->assertEquals($expectedPublicMethods, $actualPublicMethods);
    }

    /**
     * @return array
     **/
    abstract protected function getExpectedPublicMethods();

    /**
     * @return string
     **/
    abstract protected function getInterfaceUnderTest();
}
