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

class MethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateWithNoArguments()
    {
        $methodName = 'methodName';

        $invocation = new MethodInvocation($methodName);
        $this->assertSame($methodName, $invocation->getCall());
        $this->assertEquals(new MethodArguments([]), $invocation->getArguments());
        $this->assertEquals(new Metadata(), $invocation->getMetadata());
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
        $this->assertSame($methodName, $invocation->getCall());
        $this->assertEquals($arguments, $invocation->getArguments());
        $this->assertEquals($arguments->getMetadata(), $invocation->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(MethodInvocationInterface $invocation, string $expectedString)
    {
        $this->assertSame($expectedString, $invocation->render());
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
            'name only, has errors suppressed' => [
                'invocation' => $this->createInvocationWithErrorSuppression('methodName'),
                'expectedString' => '@methodName()',
            ],
        ];
    }

    private function createInvocationWithErrorSuppression(string $name): MethodInvocationInterface
    {
        $methodInvocation = new MethodInvocation($name);
        $methodInvocation->enableErrorSuppression();

        return $methodInvocation;
    }
}
