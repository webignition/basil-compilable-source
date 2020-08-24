<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Factory\ObjectConstructorFactory;
use webignition\BasilCompilableSource\MethodInvocation\ObjectConstructor;

class ObjectConstructorFactoryTest extends AbstractMethodInvocationFactoryTest
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
                'arguments' => $this->getArguments(),
                'expectedInvocation' => new ObjectConstructor(
                    $className,
                    $this->getExpectedArguments()
                ),
            ],
        ];
    }
}
