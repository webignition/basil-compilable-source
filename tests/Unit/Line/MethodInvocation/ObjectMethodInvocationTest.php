<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableName;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class ObjectMethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param ExpressionInterface $object
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     * @param MetadataInterface $expectedMetadata
     */
    public function testCreate(
        ExpressionInterface $object,
        string $methodName,
        array $arguments,
        string $argumentFormat,
        MetadataInterface $expectedMetadata
    ) {
        $invocation = new ObjectMethodInvocation($object, $methodName, $arguments, $argumentFormat);

        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame($argumentFormat, $invocation->getArgumentFormat());
        $this->assertEquals($expectedMetadata, $invocation->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'object' => VariableDependency::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'single argument' => [
                'object' => VariableDependency::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [
                    new LiteralExpression('1'),
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'multiple arguments, inline' => [
                'object' => VariableDependency::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [
                    new LiteralExpression('2'),
                    new LiteralExpression("'single-quoted value'"),
                    new LiteralExpression('"double-quoted value"'),
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'multiple arguments, stacked' => [
                'object' => VariableDependency::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [
                    new LiteralExpression('2'),
                    new LiteralExpression("'single-quoted value'"),
                    new LiteralExpression('"double-quoted value"'),
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_STACKED,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'argument expressions contain additional metadata' => [
                'object' => VariableDependency::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [
                    new StaticObjectMethodInvocation(
                        new StaticObject(ClassDependency::class),
                        'staticMethodName'
                    )
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_STACKED,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassDependency(ClassDependency::class),
                    ]),
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'no arguments, resolving placeholder' => [
                'object' => new VariableName('object'),
                'methodName' => 'method',
                'arguments' => [],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
                'expectedMetadata' => new Metadata(),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectMethodInvocation $invocation, string $expectedString)
    {
        $this->assertSame($expectedString, $invocation->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'object and method name only' => [
                'invocation' => new ObjectMethodInvocation(
                    VariableDependency::createDependency('OBJECT'),
                    'methodName'
                ),
                'expectedString' => '{{ OBJECT }}->methodName()',
            ],
            'no arguments, inline' => [
                'invocation' => new ObjectMethodInvocation(
                    VariableDependency::createDependency('OBJECT'),
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => '{{ OBJECT }}->methodName()',
            ],
            'no arguments, stacked' => [
                'invocation' => new ObjectMethodInvocation(
                    VariableDependency::createDependency('OBJECT'),
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => '{{ OBJECT }}->methodName()',
            ],
            'has arguments, inline' => [
                'invocation' => new ObjectMethodInvocation(
                    VariableDependency::createDependency('OBJECT'),
                    'methodName',
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => "{{ OBJECT }}->methodName(1, \'single-quoted value\')",
            ],
            'has arguments, stacked' => [
                'invocation' => new ObjectMethodInvocation(
                    VariableDependency::createDependency('OBJECT'),
                    'methodName',
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => "{{ OBJECT }}->methodName(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
            'object and method name only, has errors suppressed' => [
                'invocation' => $this->createInvocationWithErrorSuppression(
                    VariableDependency::createDependency('OBJECT'),
                    'methodName'
                ),
                'expectedString' => '@{{ OBJECT }}->methodName()',
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
                        VariableDependency::createDependency('OBJECT'),
                        'innerMethodName'
                    ),
                    'outerMethodName'
                ),
                'expectedString' => '{{ OBJECT }}->innerMethodName()->outerMethodName()',
            ],
            'indent stacked multi-line arguments' => [
                'invocation' => new ObjectMethodInvocation(
                    VariableDependency::createDependency('MUTATOR'),
                    'setValue',
                    [
                        new ObjectMethodInvocation(
                            VariableDependency::createDependency('NAVIGATOR'),
                            'find',
                            [
                                new StaticObjectMethodInvocation(
                                    new StaticObject(ObjectMethodInvocation::class),
                                    'fromJson',
                                    [
                                        new LiteralExpression('{' . "\n" . '    "locator": ".selector"' . "\n" . '}'),
                                    ]
                                )
                            ]
                        ),
                        new LiteralExpression('"literal for mutator"')
                    ],
                    ObjectMethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' =>
                    '{{ MUTATOR }}->setValue(' . "\n" .
                    '    {{ NAVIGATOR }}->find(ObjectMethodInvocation::fromJson({' . "\n" .
                    '        "locator": ".selector"' . "\n" .
                    '    })),' . "\n" .
                    '    "literal for mutator"' . "\n" .
                    ')'
                ,
            ],
        ];
    }

    private function createInvocationWithErrorSuppression(
        VariableDependency $objectPlaceholder,
        string $name
    ): ObjectMethodInvocation {
        $methodInvocation = new ObjectMethodInvocation($objectPlaceholder, $name);
        $methodInvocation->enableErrorSuppression();

        return $methodInvocation;
    }
}
