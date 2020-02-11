<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\MethodInvocation;

use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocationInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;

class ObjectMethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $object
     * @param string $methodName
     * @param string[] $arguments
     * @param string $argumentFormat
     */
    public function testCreate(
        string $object,
        string $methodName,
        array $arguments,
        string $argumentFormat
    ) {
        $invocation = new ObjectMethodInvocation($object, $methodName, $arguments, $argumentFormat);

        $this->assertSame($object, $invocation->getObject());
        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame($argumentFormat, $invocation->getArgumentFormat());
        $this->assertEquals(new Metadata(), $invocation->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'object' => 'object',
                'methodName' => 'method',
                'arguments' => [],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'single argument' => [
                'object' => 'object',
                'methodName' => 'method',
                'arguments' => [
                    1,
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'multiple arguments, inline' => [
                'object' => 'object',
                'methodName' => 'method',
                'arguments' => [
                    2,
                    "'single-quoted value'",
                    '"double-quoted value"'
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'multiple arguments, stacked' => [
                'object' => 'object',
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
                    'object',
                    'methodName'
                ),
                'expectedString' => 'object->methodName()',
            ],
            'no arguments, inline' => [
                'invocation' => new ObjectMethodInvocation(
                    'object',
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => 'object->methodName()',
            ],
            'no arguments, stacked' => [
                'invocation' => new ObjectMethodInvocation(
                    'object',
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => 'object->methodName()',
            ],
            'has arguments, inline' => [
                'invocation' => new ObjectMethodInvocation(
                    'object',
                    'methodName',
                    [
                        '1',
                        "\'single-quoted value\'",
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => "object->methodName(1, \'single-quoted value\')",
            ],
            'has arguments, stacked' => [
                'invocation' => new ObjectMethodInvocation(
                    'object',
                    'methodName',
                    [
                        '1',
                        "\'single-quoted value\'",
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => "object->methodName(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
        ];
    }
}
