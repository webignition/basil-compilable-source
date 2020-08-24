<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Factory\ObjectMethodInvocationFactory;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\VariableDependency;

class ObjectMethodInvocationFactoryTest extends AbstractMethodInvocationFactoryTest
{
    private ObjectMethodInvocationFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = ObjectMethodInvocationFactory::createFactory();
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param string $methodName
     * @param array<mixed> $arguments
     * @param ObjectMethodInvocation $expectedInvocation
     */
    public function testCreate(
        ExpressionInterface $object,
        string $methodName,
        array $arguments,
        ObjectMethodInvocation $expectedInvocation
    ) {
        self::assertEquals($expectedInvocation, $this->factory->create($object, $methodName, $arguments));
    }

    public function createDataProvider(): array
    {
        $object = new VariableDependency('DEPENDENCY');

        return [
            'empty' => [
                'object' => $object,
                'methodName' => 'methodName',
                'arguments' => [],
                'expectedInvocation' => new ObjectMethodInvocation($object, 'methodName'),
            ],
            'non-empty' => [
                'object' => new VariableDependency('DEPENDENCY'),
                'methodName' => 'methodName',
                'arguments' => $this->getArguments(),
                'expectedInvocation' => new ObjectMethodInvocation(
                    $object,
                    'methodName',
                    $this->getExpectedArguments()
                ),
            ],
        ];
    }
}
