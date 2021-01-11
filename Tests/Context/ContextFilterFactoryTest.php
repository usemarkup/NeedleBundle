<?php
declare(strict_types=1);

namespace Context;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Attribute\SpecializationContextHashInterface;
use Markup\NeedleBundle\Context\ContextFilterFactory;
use Markup\NeedleBundle\Facet\RangeFacetField;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\RangeFilterValue;
use Markup\NeedleBundle\Filter\UnionFilterValue;
use PHPUnit\Framework\TestCase;

class ContextFilterFactoryTest extends TestCase
{
    /**
     * @dataProvider rangeUnionDataProvider
     *
     * @param float $rangeSize
     * @param mixed $filterValue
     * @param FilterValueInterface|null $expectedFilterValue
     * @param bool $throwsException
     */
    public function testRangeUnion(
        $filterValue,
        float $rangeSize,
        ?FilterValueInterface $expectedFilterValue,
        bool $throwsException
    ): void {
        $attributeMock = $this->createMock(RangeFacetField::class);
        $attributeMock->method('getRangeSize')->willReturn($rangeSize);

        $attributeProviderMock = $this->createMock(AttributeProviderInterface::class);
        $attributeProviderMock->method('getAttributeByName')->willReturn($attributeMock);

        $specializationContextHashMock = $this->createMock(SpecializationContextHashInterface::class);

        $factory = new ContextFilterFactory($attributeProviderMock);
        if ($throwsException) {
            $this->expectException(\RuntimeException::class);
        }
        $result = $factory->create('', $filterValue, $specializationContextHashMock);
        if (!$result) {
            $this->assertSame($expectedFilterValue, $result);
        } else {
            $this->assertEquals($expectedFilterValue, $result->getFilterValue());
        }
    }

    public function rangeUnionDataProvider(): array
    {
        return [
            'two simple values' => [
                ['50', '150'],
                50,
                new UnionFilterValue([
                    new RangeFilterValue(50, 99.99),
                    new RangeFilterValue(150, 199.99),
                ]),
                false,
            ],
            'multiple overlapping values not in order' => [
                ['87.51', '42.84', '150.01'],
                50.5,
                new UnionFilterValue([
                    new RangeFilterValue(87.51, 138),
                    new RangeFilterValue(42.84, 93.33),
                    new RangeFilterValue(150.01, 200.5),
                ]),
                false,
            ],
            'one value' => [
                ['75'],
                2.5,
                new RangeFilterValue(75, 77.49),
                false,
            ],
            'explicit min and max' => [
                ['min' => 15.2, 'max' => 99.5],
                -256,
                new RangeFilterValue(15.2, 99.5),
                false,
            ],
            'zero values' => [
                [],
                50,
                null,
                true,
            ],
            'value not an array' => [
                '150',
                50,
                null,
                true,
            ]
        ];
    }
}