<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetField;
use PHPUnit\Framework\TestCase;

/**
* A test for a facet field implementation.
*/
class FacetFieldTest extends TestCase
{
    public function setUp()
    {
        $this->filter = $this->createMock(AttributeInterface::class);
        $this->facet = new FacetField($this->filter);
    }

    public function testIsAttribute()
    {
        $this->assertInstanceOf(AttributeInterface::class, $this->facet);
    }

    public function testGetName()
    {
        $name = 'xyzzy';
        $this->filter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $this->assertEquals($name, $this->facet->getName());
    }

    public function testGetDisplayName()
    {
        $display = 'Xyzzy';
        $this->filter
            ->expects($this->any())
            ->method('getDisplayName')
            ->will($this->returnValue($display));
        $this->assertEquals($display, $this->facet->getDisplayName());
    }

    public function testGetSearchKey()
    {
        $key = 'xyzzy';
        $this->filter
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue($key));
        $this->assertEquals($key, $this->facet->getSearchKey());
    }

    public function testCastToString()
    {
        $display = 'Xyzzy';
        $this->filter
            ->expects($this->any())
            ->method('getDisplayName')
            ->will($this->returnValue($display));
        $this->assertEquals($display, (string) $this->facet);
    }
}
