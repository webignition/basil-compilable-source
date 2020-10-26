<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodInvocation;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocationInterface;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;

class MethodInvocationTest extends AbstractResolvableTest
{
    public function testCreateWithNoArguments()
    {
        $methodName = 'methodName';

        $invocation = new MethodInvocation($methodName);
        self::assertSame($methodName, $invocation->getCall());
        self::assertEquals(new Metadata(), $invocation->getMetadata());
    }

    public function testCreateWithArgumentsWithMetadata()
    {
        $methodName = 'methodName';
        $arguments = new MethodArguments([
            new StaticObjectMethodInvocation(
                new StaticObject(ClassName::class),
                'staticMethodName'
            )
        ]);

        $invocation = new MethodInvocation($methodName, $arguments);
        self::assertSame($methodName, $invocation->getCall());
        self::assertSame($arguments, $invocation->getArguments());
        self::assertEquals($arguments->getMetadata(), $invocation->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(MethodInvocationInterface $invocation, string $expectedString)
    {
        self::assertRenderResolvable($expectedString, $invocation);
    }

    public function renderDataProvider(): array
    {
        return [
            'no arguments' => [
                'invocation' => new MethodInvocation('methodName'),
                'expectedString' => 'methodName()',
            ],
            'has arguments, inline' => [
                'invocation' => new MethodInvocation(
                    'methodName',
                    new MethodArguments([
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ])
                ),
                'expectedString' => "methodName(1, \'single-quoted value\')",
            ],
            'has arguments, stacked' => [
                'invocation' => new MethodInvocation(
                    'methodName',
                    new MethodArguments(
                        [
                            new LiteralExpression('1'),
                            new LiteralExpression("\'single-quoted value\'"),
                        ],
                        MethodArguments::FORMAT_STACKED
                    )
                ),
                'expectedString' => "methodName(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
        ];
    }
}
