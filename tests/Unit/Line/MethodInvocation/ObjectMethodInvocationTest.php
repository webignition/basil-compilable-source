<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocationInterface;
use webignition\BasilCompilableSource\Line\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class ObjectMethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param VariablePlaceholder $objectPlaceholder
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     * @param MetadataInterface $expectedMetadata
     */
    public function testCreate(
        VariablePlaceholder $objectPlaceholder,
        string $methodName,
        array $arguments,
        string $argumentFormat,
        MetadataInterface $expectedMetadata
    ) {
        $invocation = new ObjectMethodInvocation($objectPlaceholder, $methodName, $arguments, $argumentFormat);

        $this->assertSame($objectPlaceholder, $invocation->getObjectPlaceholder());
        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame($argumentFormat, $invocation->getArgumentFormat());
        $this->assertEquals($expectedMetadata, $invocation->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
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
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
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
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
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
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
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
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
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
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectMethodInvocationInterface $invocation, string $expectedString)
    {
        $this->assertSame($expectedString, $invocation->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'object and method name only' => [
                'invocation' => new ObjectMethodInvocation(
                    VariablePlaceholder::createDependency('OBJECT'),
                    'methodName'
                ),
                'expectedString' => '{{ OBJECT }}->methodName()',
            ],
            'no arguments, inline' => [
                'invocation' => new ObjectMethodInvocation(
                    VariablePlaceholder::createDependency('OBJECT'),
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => '{{ OBJECT }}->methodName()',
            ],
            'no arguments, stacked' => [
                'invocation' => new ObjectMethodInvocation(
                    VariablePlaceholder::createDependency('OBJECT'),
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => '{{ OBJECT }}->methodName()',
            ],
            'has arguments, inline' => [
                'invocation' => new ObjectMethodInvocation(
                    VariablePlaceholder::createDependency('OBJECT'),
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
                    VariablePlaceholder::createDependency('OBJECT'),
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
                    VariablePlaceholder::createDependency('OBJECT'),
                    'methodName'
                ),
                'expectedString' => '@{{ OBJECT }}->methodName()',
            ],
        ];
    }

    private function createInvocationWithErrorSuppression(
        VariablePlaceholder $objectPlaceholder,
        string $name
    ): ObjectMethodInvocation {
        $methodInvocation = new ObjectMethodInvocation($objectPlaceholder, $name);
        $methodInvocation->enableErrorSuppression();

        return $methodInvocation;
    }
}
