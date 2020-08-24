<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Factory\MethodInvocationFactory;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\StaticObject;

class MethodInvocationFactoryTest extends \PHPUnit\Framework\TestCase
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
                'expectedInvocation' => new MethodInvocation(
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
