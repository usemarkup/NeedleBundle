<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Context\DecorateContextTrait;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class DecorateContextTraitTest extends MockeryTestCase
{
    /**
     * @var SearchContextInterface|m\MockInterface
     */
    private $decorated;

    /**
     * @var SearchContextInterface
     */
    private $context;

    protected function setUp()
    {
        $this->decorated = m::spy(SearchContextInterface::class);
        $decorated = $this->decorated;
        $this->context = new class ($decorated) implements SearchContextInterface {
            use DecorateContextTrait;

            public function __construct(SearchContextInterface $context)
            {
                $this->context = $context;
            }
        };
    }

    public function testPublicMethodsDelegateDown()
    {
        $reflection = new \ReflectionObject($this->context);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod) {
            if ($publicMethod->getName() === '__construct') {
                continue;
            }
            $thing = 'a thing';
            $mockedArgs = $this->getMockedArgsForMethod($publicMethod);
            $this->decorated
                ->shouldReceive($publicMethod->getName())
                ->withArgs($mockedArgs)
                ->andReturn($thing);
            $this->assertSame($thing, $this->context->{$publicMethod->getName()}(...$mockedArgs));
        }
    }

    private function getMockedArgsForMethod(\ReflectionMethod $method): array
    {
        return array_map(
            function (\ReflectionParameter $param) {
                //this works because only method params for this interface are typed objects
                return m::mock($param->getType()->getName());
            },
            $method->getParameters()
        );
    }
}
