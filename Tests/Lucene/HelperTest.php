<?php

namespace Markup\NeedleBundle\Tests\Lucene;

use Markup\NeedleBundle\Exception\LuceneSyntaxException;
use Markup\NeedleBundle\Lucene\Helper;
use Markup\NeedleBundle\Lucene\HelperInterface;
use Solarium\Core\Query\Helper as SolariumHelper;
use Solarium\Exception\InvalidArgumentException;

/**
* A test for a Lucene helper implementation that uses a helper implementation in Solarium.
*/
class HelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SolariumHelper
     */
    private $solariumHelper;

    /**
     * @var Helper
     */
    private $helper;

    public function setUp()
    {
        $this->solariumHelper = $this->createMock(SolariumHelper::class);
        $this->helper = new Helper($this->solariumHelper);
    }

    public function testIsHelper()
    {
        $this->assertInstanceOf(HelperInterface::class, $this->helper);
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
