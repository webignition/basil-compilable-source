<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Factory\ObjectConstructorFactory;
use webignition\BasilCompilableSource\MethodInvocation\ObjectConstructor;
use webignition\BasilCompilableSource\StaticObject;

class ObjectConstructorFactoryTest extends \PHPUnit\Framework\TestCase
{
    private ObjectConstructorFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = ObjectConstructorFactory::createFactory();
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param ClassName $className
     * @param array<mixed> $arguments
     * @param ObjectConstructor $expectedInvocation
     */
    public function testCreate(
        ClassName $className,
        array $arguments,
        ObjectConstructor $expectedInvocation
    ) {
        self::assertEquals($expectedInvocation, $this->factory->create($className, $arguments));
    }

    public function createDataProvider(): array
    {
        $className = new ClassName('Acme\\Class');

        return [
            'empty' => [
                'className' => $className,
                'arguments' => [],
                'expectedInvocation' => new ObjectConstructor($className),
            ],
            'non-empty' => [
                'className' => $className,
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
                'expectedInvocation' => new ObjectConstructor(
                    $className,
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
