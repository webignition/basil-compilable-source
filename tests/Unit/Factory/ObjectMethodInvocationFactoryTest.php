<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Factory\ObjectMethodInvocationFactory;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariableDependency;

class ObjectMethodInvocationFactoryTest extends \PHPUnit\Framework\TestCase
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
                'arguments' => [
                    100,
                    M_PI,
                    'string without single quotes',
                    'string with \'single\' quotes',
                    true,
                    false,
                    new \stdClass(),
                    new StaticObject('self'),
                ],
                'expectedInvocation' => new ObjectMethodInvocation(
                    $object,
                    'methodName',
                    [
                        new LiteralExpression('100'),
                        new LiteralExpression((string) M_PI),
                        new LiteralExpression('\'string without single quotes\''),
                        new LiteralExpression('\'string with \\\'single\\\' quotes\''),
                        new LiteralExpression('true'),
                        new LiteralExpression('false'),
                        new StaticObject('self'),
                    ]
                ),
            ],
        ];
    }
}
