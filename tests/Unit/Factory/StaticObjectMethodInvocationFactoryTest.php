<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\Factory\StaticObjectMethodInvocationFactory;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectMethodInvocationFactoryTest extends AbstractMethodInvocationFactoryTest
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
                'arguments' => $this->getArguments(),
                'expectedInvocation' => new StaticObjectMethodInvocation(
                    $staticObject,
                    'methodName',
                    $this->getExpectedArguments()
                ),
            ],
        ];
    }
}
