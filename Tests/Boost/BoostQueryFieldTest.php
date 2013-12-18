<?php

namespace Markup\NeedleBundle\Tests\Boost;

use Markup\NeedleBundle\Boost\BoostQueryField;

/**
* A test for a boost query field object.
*/
class BoostQueryFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testIsBoostQueryField()
    {
        $field = new \ReflectionClass('Markup\NeedleBundle\Boost\BoostQueryField');
        $this->assertTrue($field->implementsInterface('Markup\NeedleBundle\Boost\BoostQueryFieldInterface'));
    }

    public function testGetAttributeKey()
    {
        $attrKey = 'boots';
        $field = new BoostQueryField($attrKey);
        $this->assertEquals($attrKey, $field->getAttributeKey());
    }

    public function testGetBoost()
    {
        $boostFactor = 42;
        $field = new BoostQueryField('attr', $boostFactor);
        $this->assertEquals($boostFactor, $field->getBoostFactor());
    }
}
