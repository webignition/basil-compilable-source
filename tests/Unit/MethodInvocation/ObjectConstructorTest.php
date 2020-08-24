<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectConstructor;

class ObjectConstructorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param \webignition\BasilCompilableSource\ClassName $class
     * @param ExpressionInterface[] $arguments
     * @param MetadataInterface $expectedMetadata
     */
    public function testCreate(
        ClassName $class,
        array $arguments,
        MetadataInterface $expectedMetadata
    ) {
        $constructor = new ObjectConstructor($class, $arguments);

        $this->assertSame($class->getClass(), $constructor->getMethodName());
        $this->assertSame($arguments, $constructor->getArguments());
        $this->assertSame(ObjectConstructor::ARGUMENT_FORMAT_INLINE, $constructor->getArgumentFormat());
        $this->assertEquals($expectedMetadata, $constructor->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'class' => new ClassName(ObjectConstructor::class),
                'arguments' => [],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ObjectConstructor::class)
                    ]),
                ]),
            ],
            'single argument' => [
                'class' => new ClassName(ObjectConstructor::class),
                'arguments' => [
                    new LiteralExpression('1'),
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ObjectConstructor::class)
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectConstructor $constructor, string $expectedString)
    {
        $this->assertSame($expectedString, $constructor->render());
    }

    public function renderDataProvider(): array
    {
        $classDependency = new ClassName('Acme\\Model');

        return [
            'no arguments, inline' => [
                'constructor' => (new ObjectConstructor($classDependency))->withInlineArguments(),
                'expectedString' => 'new Model()',
            ],
            'no arguments, inline, class in root namespace' => [
                'constructor' => new ObjectConstructor(
                    new ClassName(\Exception::class)
                ),
                'expectedString' => 'new \Exception()',
            ],
            'no arguments, stacked' => [
                'constructor' => (new ObjectConstructor($classDependency))->withStackedArguments(),
                'expectedString' => 'new Model()',
            ],
            'has arguments, inline' => [
                'constructor' => (new ObjectConstructor(
                    $classDependency,
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ]
                ))->withInlineArguments(),
                'expectedString' => "new Model(1, \'single-quoted value\')",
            ],
            'has arguments, stacked' => [
                'constructor' => (new ObjectConstructor(
                    $classDependency,
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ]
                ))->withStackedArguments(),
                'expectedString' => "new Model(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
        ];
    }
}
