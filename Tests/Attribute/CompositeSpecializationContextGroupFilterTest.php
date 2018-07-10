<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\CompositeSpecializationContextGroupFilter;
use Markup\NeedleBundle\Attribute\SpecializationContextGroupFilterInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CompositeSpecializationContextGroupFilterTest extends MockeryTestCase
{
    /**
     * @var CompositeSpecializationContextGroupFilter
     */
    private $composite;

    protected function setUp()
    {
        $this->composite = new CompositeSpecializationContextGroupFilter();
    }

    public function testIsContextGroupFilter()
    {
        $this->assertInstanceOf(SpecializationContextGroupFilterInterface::class, $this->composite);
    }

    public function testAcceptReturnsTrueWhenEmptyComposite()
    {
        $data = [
            'attr1' => 'value1',
            'attr2' => 'value2',
            'attr3' => 'value3',
        ];
        $this->assertTrue($this->composite->accept($data));
    }

    public function testAcceptReturnsDelegatedAnswerFromAddedFilter()
    {
        $data = [
            'attr1' => 'value1',
            'attr2' => 'value2',
            'attr3' => 'value3',
        ];
        $filter = $this->getMockFilter(true, $data);
        $this->composite->addFilter($filter);

        $this->assertTrue($this->composite->accept($data));
    }

    public function testAcceptReturnsFalseWhenOneFilterAcceptsAndOneRejects()
    {
        $data = [
            'attr1' => 'value1',
            'attr2' => 'value2',
            'attr3' => 'value3',
        ];
        $this->composite->addFilter($this->getMockFilter(true, $data));
        $this->composite->addFilter($this->getMockFilter(false, $data));

        $this->assertFalse($this->composite->accept($data));
    }

    private function getMockFilter(bool $whetherAccept, array $data)
    {
        return m::mock(SpecializationContextGroupFilterInterface::class)
            ->shouldReceive('accept')
            ->with($data)
            ->once()
            ->andReturn($whetherAccept)
            ->getMock();
    }
}
