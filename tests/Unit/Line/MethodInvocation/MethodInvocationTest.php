<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\MethodInvocation;

use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocationInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;

class MethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $methodName
     * @param string[] $arguments
     * @param string $argumentFormat
     */
    public function testCreate(
        string $methodName,
        array $arguments,
        string $argumentFormat
    ) {
        $invocation = new MethodInvocation($methodName, $arguments, $argumentFormat);

        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame($argumentFormat, $invocation->getArgumentFormat());
        $this->assertEquals(new Metadata(), $invocation->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'methodName' => 'method',
                'arguments' => [],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'single argument' => [
                'methodName' => 'method',
                'arguments' => [
                    1,
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'multiple arguments, inline' => [
                'methodName' => 'method',
                'arguments' => [
                    2,
                    "'single-quoted value'",
                    '"double-quoted value"'
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
            ],
            'multiple arguments, stacked' => [
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
    public function testRender(MethodInvocationInterface $methodInvocation, string $expectedString)
    {
        $this->assertSame($expectedString, $methodInvocation->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'name only' => [
                'methodInvocation' => new MethodInvocation(
                    'methodName'
                ),
                'expectedString' => 'methodName()',
            ],
            'no arguments, inline' => [
                'methodInvocation' => new MethodInvocation(
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => 'methodName()',
            ],
            'no arguments, stacked' => [
                'methodInvocation' => new MethodInvocation(
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => 'methodName()',
            ],
            'has arguments, inline' => [
                'methodInvocation' => new MethodInvocation(
                    'methodName',
                    [
                        '1',
                        "\'single-quoted value\'",
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => "methodName(1, \'single-quoted value\')",
            ],
            'has arguments, stacked' => [
                'methodInvocation' => new MethodInvocation(
                    'methodName',
                    [
                        '1',
                        "\'single-quoted value\'",
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => "methodName(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
        ];
    }
}
