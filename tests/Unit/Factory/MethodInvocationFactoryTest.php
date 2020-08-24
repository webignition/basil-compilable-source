<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\Factory\MethodInvocationFactory;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;

class MethodInvocationFactoryTest extends AbstractMethodInvocationFactoryTest
{
    private MethodInvocationFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = MethodInvocationFactory::createFactory();
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param string $methodName
     * @param array<mixed> $arguments
     * @param MethodInvocation $expectedInvocation
     */
    public function testCreate(
        string $methodName,
        array $arguments,
        MethodInvocation $expectedInvocation
    ) {
        self::assertEquals($expectedInvocation, $this->factory->create($methodName, $arguments));
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'methodName' => 'methodName',
                'arguments' => [],
                'expectedInvocation' => new MethodInvocation('methodName'),
            ],
            'non-empty' => [
                'methodName' => 'methodName',
                'arguments' => $this->getArguments(),
                'expectedInvocation' => new MethodInvocation(
                    'methodName',
                    $this->getExpectedArguments()
                ),
            ],
        ];
    }
}
