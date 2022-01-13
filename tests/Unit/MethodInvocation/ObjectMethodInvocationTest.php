<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;

class ObjectMethodInvocationTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ExpressionInterface $object,
        string $methodName,
        ?MethodArgumentsInterface $arguments,
        MetadataInterface $expectedMetadata
    ): void {
        $invocation = new ObjectMethodInvocation($object, $methodName, $arguments);

        $this->assertSame($methodName, $invocation->getCall());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertEquals($expectedMetadata, $invocation->getMetadata());
    }

    /**
     * @return array<mixed>
     */
    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'object' => new VariableDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => new MethodArguments(),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'has arguments' => [
                'object' => new VariableDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => new MethodArguments([
                    new LiteralExpression('1'),
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'argument expressions contain additional metadata' => [
                'object' => new VariableDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => new MethodArguments([
                    new StaticObjectMethodInvocation(
                        new StaticObject(ClassName::class),
                        'staticMethodName'
                    )
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ClassName::class),
                    ]),
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'no arguments, resolving placeholder' => [
                'object' => new VariableName('object'),
                'methodName' => 'method',
                'arguments' => new MethodArguments(),
                'expectedMetadata' => new Metadata(),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectMethodInvocation $invocation, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $invocation);
    }

    /**
     * @return array<mixed>
     */
    public function renderDataProvider(): array
    {
        return [
            'object and method name only' => [
                'invocation' => new ObjectMethodInvocation(
                    new VariableDependency('OBJECT'),
                    'methodName'
                ),
                'expectedString' => '{{ OBJECT }}->methodName()',
            ],
            'has arguments, inline' => [
                'invocation' => new ObjectMethodInvocation(
                    new VariableDependency('OBJECT'),
                    'methodName',
                    new MethodArguments([
                        new LiteralExpression('1'),
                        new LiteralExpression("\\'single-quoted value\\'"),
                    ])
                ),
                'expectedString' => "{{ OBJECT }}->methodName(1, \\'single-quoted value\\')",
            ],
            'has arguments, stacked' => [
                'invocation' => new ObjectMethodInvocation(
                    new VariableDependency('OBJECT'),
                    'methodName',
                    new MethodArguments(
                        [
                            new LiteralExpression('1'),
                            new LiteralExpression("\\'single-quoted value\\'"),
                        ],
                        MethodArguments::FORMAT_STACKED
                    )
                ),
                'expectedString' => "{{ OBJECT }}->methodName(\n" .
                    "    1,\n" .
                    "    \\'single-quoted value\\'\n" .
                    ')',
            ],
            'object and method name only, resolving placeholder' => [
                'invocation' => new ObjectMethodInvocation(
                    new VariableName('object'),
                    'methodName'
                ),
                'expectedString' => '$object->methodName()',
            ],
            'object returned from method call' => [
                'invocation' => new ObjectMethodInvocation(
                    new MethodInvocation(
                        'literalMethodName'
                    ),
                    'objectMethodName'
                ),
                'expectedString' => 'literalMethodName()->objectMethodName()',
            ],
            'object returned from object method call' => [
                'invocation' => new ObjectMethodInvocation(
                    new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'innerMethodName'
                    ),
                    'outerMethodName'
                ),
                'expectedString' => '{{ OBJECT }}->innerMethodName()->outerMethodName()',
            ],
        ];
    }
}
