<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Lucene;

use Markup\NeedleBundle\Attribute\Attribute;
use Markup\NeedleBundle\Boost\BoostQueryField;
use Markup\NeedleBundle\Lucene\BoostLucenifier;
use PHPUnit\Framework\TestCase;

class BoostLucenifierTest extends TestCase
{
    /**
     * @dataProvider boosts
     */
    public function testLucenifyBoost($attr, $boostFactor, $expected)
    {
        $boostField = new BoostQueryField(new Attribute($attr), $boostFactor);
        $this->assertEquals($expected, (new BoostLucenifier())->lucenifyBoost($boostField));
    }

    public function boosts()
    {
        return [
            ['attr', 3, 'attr^3'],
            ['color', 1, 'color'],
        ];
    }
}
