<?php

namespace Markup\NeedleBundle\Tests\Lucene;

use Markup\NeedleBundle\Exception\LuceneSyntaxException;
use Markup\NeedleBundle\Lucene\Helper;
use Solarium\Exception\InvalidArgumentException;

/**
* A test for a Lucene helper implementation that uses a helper implementation in Solarium.
*/
class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->solariumHelper = $this->getMockBuilder('Solarium\Core\Query\Helper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = new Helper($this->solariumHelper);
    }

    public function testIsHelper()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Lucene\HelperInterface', $this->helper);
    }

    public function testAssembleCallsDownOnHelper()
    {
        $query = 'query';
        $parts = ['yes', 'no'];
        $assembled = 'assembled';
        $this->solariumHelper
            ->expects($this->once())
            ->method('assemble')
            ->with($this->equalTo($query), $this->equalTo($parts))
            ->will($this->returnValue($assembled));
        $actualAssembled = $this->helper->assemble($query, $parts);
        $this->assertEquals($assembled, $actualAssembled);
    }

    public function testSolariumExceptionCausesLuceneSyntaxException()
    {
        $query = 'query';
        $parts = ['yes', 'no'];
        $this->solariumHelper
            ->expects($this->once())
            ->method('assemble')
            ->with($this->equalTo($query), $this->equalTo($parts))
            ->will($this->throwException(new InvalidArgumentException()));
        $this->setExpectedException(LuceneSyntaxException::class);
        $this->helper->assemble($query, $parts);
    }
}
