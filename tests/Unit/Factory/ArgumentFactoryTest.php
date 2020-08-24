<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Factory\ArgumentFactory;

class ArgumentFactoryTest extends AbstractMethodInvocationFactoryTest
{
    private ArgumentFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = ArgumentFactory::createFactory();
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param array<mixed> $arguments
     * @param ExpressionInterface[] $expectedArguments
     */
    public function testCreate(array $arguments, array $expectedArguments)
    {
        self::assertEquals($expectedArguments, $this->factory->create($arguments));
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'arguments' => [],
                'expectedArguments' => [],
            ],
            'non-empty' => [
                'arguments' => $this->getArguments(),
                'expectedArguments' => $this->getExpectedArguments(),
            ],
        ];
    }
}
