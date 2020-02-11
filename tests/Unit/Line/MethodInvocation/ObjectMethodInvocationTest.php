<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\MethodInvocation;

use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocationInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class ObjectMethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param VariablePlaceholder $objectPlaceholder
     * @param string $methodName
     * @param string[] $arguments
     * @param string $argumentFormat
     */
    public function testCreate(
        VariablePlaceholder $objectPlaceholder,
        string $methodName,
        array $arguments,
        string $argumentFormat
    ) {
        $invocation = new ObjectMethodInvocation($objectPlaceholder, $methodName, $arguments, $argumentFormat);

        $this->assertSame($objectPlaceholder, $invocation->getObjectPlaceholder());
        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame($argumentFormat, $invocation->getArgumentFormat());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'single argument' => [
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [
                    1,
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'multiple arguments, inline' => [
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [
                    2,
                    "'single-quoted value'",
                    '"double-quoted value"'
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'multiple arguments, stacked' => [
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
                'methodName' => 'method',
                'arguments' => [
                    2,
                    "'single-quoted value'",
                    '"double-quoted value"'
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_STACKED,
            ],
        ];
    }

    public function testGetMetadata()
    {
        $invocation = new ObjectMethodInvocation(
            VariablePlaceholder::createDependency('OBJECT'),
            'methodName'
        );

        $this->assertEquals(
            new Metadata([
                Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                    'OBJECT',
                ])
            ]),
            $invocation->getMetadata()
        );
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
                        '1',
                        "\'single-quoted value\'",
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
                        '1',
                        "\'single-quoted value\'",
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => "{{ OBJECT }}->methodName(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
        ];
    }
}
