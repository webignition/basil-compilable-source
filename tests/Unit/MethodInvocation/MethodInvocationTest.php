<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\EmptyLine;
use webignition\BasilCompilableSource\Expression\ClosureExpression;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocationInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Statement\ReturnStatement;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableName;

class MethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     * @param MetadataInterface $expectedMetadata
     */
    public function testCreate(
        string $methodName,
        array $arguments,
        MetadataInterface $expectedMetadata
    ) {
        $invocation = new MethodInvocation($methodName, $arguments);

        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame(MethodInvocation::ARGUMENT_FORMAT_INLINE, $invocation->getArgumentFormat());
        $this->assertEquals($expectedMetadata, $invocation->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'methodName' => 'method',
                'arguments' => [],
                'expectedMetadata' => new Metadata(),
            ],
            'single argument' => [
                'methodName' => 'method',
                'arguments' => [
                    new LiteralExpression('1'),
                ],
                'expectedMetadata' => new Metadata(),
            ],
            'multiple arguments' => [
                'methodName' => 'method',
                'arguments' => [
                    new LiteralExpression('2'),
                    new LiteralExpression("\'single-quoted value\'"),
                    new LiteralExpression('"double-quoted value"'),
                ],
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'methodName' => 'methodName',
                'arguments' => [
                    new \webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation(
                        new StaticObject(ClassName::class),
                        'staticMethodName'
                    )
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ClassName::class),
                    ])
                ]),
            ],
        ];
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
            'no arguments, inline' => [
                'invocation' => (new MethodInvocation('methodName'))->withInlineArguments(),
                'expectedString' => 'methodName()',
            ],
            'no arguments, stacked' => [
                'invocation' => (new MethodInvocation('methodName'))->withStackedArguments(),
                'expectedString' => 'methodName()',
            ],
            'has arguments, inline' => [
                'invocation' => (new MethodInvocation(
                    'methodName',
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ]
                ))->withInlineArguments(),
                'expectedString' => "methodName(1, \'single-quoted value\')",
            ],
            'has arguments, stacked' => [
                'invocation' => (new MethodInvocation(
                    'methodName',
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ]
                ))->withStackedArguments(),
                'expectedString' => "methodName(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
            'name only, has errors suppressed' => [
                'invocation' => $this->createInvocationWithErrorSuppression('methodName'),
                'expectedString' => '@methodName()',
            ],
            'indent stacked multi-line arguments' => [
                'invocation' => (new MethodInvocation(
                    'setValue',
                    [
                        new ObjectMethodInvocation(
                            new VariableDependency('NAVIGATOR'),
                            'find',
                            [
                                new \webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation(
                                    new StaticObject(ObjectMethodInvocation::class),
                                    'fromJson',
                                    [
                                        new LiteralExpression('{' . "\n" . '    "locator": ".selector"' . "\n" . '}'),
                                    ]
                                )
                            ]
                        ),
                        new ClosureExpression(
                            new Body([
                                new AssignmentStatement(
                                    new VariableName('variable'),
                                    new LiteralExpression('100')
                                ),
                                new EmptyLine(),
                                new ReturnStatement(
                                    new VariableName('variable'),
                                ),
                            ])
                        ),
                    ]
                ))->withStackedArguments(),
                'expectedString' =>
                    'setValue(' . "\n" .
                    '    {{ NAVIGATOR }}->find(ObjectMethodInvocation::fromJson({' . "\n" .
                    '        "locator": ".selector"' . "\n" .
                    '    })),' . "\n" .
                    '    (function () {' . "\n" .
                    '        $variable = 100;' . "\n" .
                    "\n" .
                    '        return $variable;' . "\n" .
                    '    })()' . "\n" .
                    ')',
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
