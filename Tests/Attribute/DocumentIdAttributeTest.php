<?php

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\DocumentIdAttribute;

class DocumentIdAttributeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAttribute()
    {
        $attr = new DocumentIdAttribute();
        $this->assertInstanceOf('Markup\NeedleBundle\Attribute\AttributeInterface', $attr);
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
