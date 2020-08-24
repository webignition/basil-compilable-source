<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Factory\StaticObjectMethodInvocationFactory;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectMethodInvocationFactoryTest extends \PHPUnit\Framework\TestCase
{
    private StaticObjectMethodInvocationFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = StaticObjectMethodInvocationFactory::createFactory();
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param string $methodName
     * @param array<mixed> $arguments
     * @param MethodInvocation $expectedInvocation
     */
    public function testCreate(
        StaticObject $staticObject,
        string $methodName,
        array $arguments,
        MethodInvocation $expectedInvocation
    ) {
        self::assertEquals($expectedInvocation, $this->factory->create($staticObject, $methodName, $arguments));
    }

    public function createDataProvider(): array
    {
        $staticObject = new StaticObject('self');

        return [
            'empty' => [
                'staticObject' => $staticObject,
                'methodName' => 'methodName',
                'arguments' => [],
                'expectedInvocation' => new StaticObjectMethodInvocation($staticObject, 'methodName'),
            ],
            'non-empty' => [
                'staticObject' => $staticObject,
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
                'expectedInvocation' => new StaticObjectMethodInvocation(
                    $staticObject,
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
