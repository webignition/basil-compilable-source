<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectConstructor;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;

class ObjectConstructorTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ClassName $class,
        ?MethodArgumentsInterface $arguments,
        MetadataInterface $expectedMetadata
    ): void {
        $constructor = new ObjectConstructor($class, $arguments);

        $this->assertSame($class->getClass(), $constructor->getCall());
        $this->assertSame($arguments, $constructor->getArguments());
        $this->assertEquals($expectedMetadata, $constructor->getMetadata());
    }

    /**
     * @return array<mixed>
     */
    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'class' => new ClassName(ObjectConstructor::class),
                'arguments' => new MethodArguments(),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ObjectConstructor::class)
                    ]),
                ]),
            ],
            'single argument' => [
                'class' => new ClassName(ObjectConstructor::class),
                'arguments' => new MethodArguments([
                    new LiteralExpression('1'),
                ]),
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
    public function testRender(ObjectConstructor $constructor, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $constructor);
    }

    /**
     * @return array<mixed>
     */
    public function renderDataProvider(): array
    {
        $classDependency = new ClassName('Acme\\Model');

        return [
            'no arguments' => [
                'constructor' => new ObjectConstructor($classDependency),
                'expectedString' => 'new Model()',
            ],
            'no arguments, class in root namespace' => [
                'constructor' => new ObjectConstructor(
                    new ClassName(\Exception::class)
                ),
                'expectedString' => 'new \Exception()',
            ],
            'has arguments, inline' => [
                'constructor' => new ObjectConstructor(
                    $classDependency,
                    new MethodArguments([
                        new LiteralExpression('1'),
                        new LiteralExpression("\\'single-quoted value\\'"),
                    ])
                ),
                'expectedString' => "new Model(1, \\'single-quoted value\\')",
            ],
            'has arguments, stacked' => [
                'constructor' => new ObjectConstructor(
                    $classDependency,
                    new MethodArguments(
                        [
                            new LiteralExpression('1'),
                            new LiteralExpression("\\'single-quoted value\\'"),
                        ],
                        MethodArguments::FORMAT_STACKED
                    )
                ),
                'expectedString' => "new Model(\n" .
                    "    1,\n" .
                    "    \\'single-quoted value\\'\n" .
                    ')',
            ],
        ];
    }
}
