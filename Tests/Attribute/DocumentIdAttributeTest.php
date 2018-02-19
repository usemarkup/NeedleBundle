<?php

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\DocumentIdAttribute;
use PHPUnit\Framework\TestCase;

class DocumentIdAttributeTest extends TestCase
{
    public function testIsAttribute()
    {
        $attr = new DocumentIdAttribute();
        $this->assertInstanceOf(AttributeInterface::class, $attr);
    }

    public function testGetSearchKeyByDefault()
    {
        $attr = new DocumentIdAttribute();
        $this->assertEquals('id', $attr->getSearchKey());
    }

    public function testGetChosenSearchKey()
    {
        $id = 'my_id';
        $attr = new DocumentIdAttribute($id);
        $this->assertEquals($id, $attr->getSearchKey());
    }
}
