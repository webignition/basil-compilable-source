<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodArguments;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\EmptyLine;
use webignition\BasilCompilableSource\Expression\ArrayExpression;
use webignition\BasilCompilableSource\Expression\AssignmentExpression;
use webignition\BasilCompilableSource\Expression\ClosureExpression;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Expression\ReturnExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableName;

class MethodArgumentsTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param ExpressionInterface[] $arguments
     */
    public function testCreate(
        array $arguments,
        string $format,
        MetadataInterface $expectedMetadata
    ): void {
        $methodArguments = new MethodArguments($arguments, $format);

        $this->assertSame($arguments, $methodArguments->getArguments());
        $this->assertSame($format, $methodArguments->getFormat());
        $this->assertEquals($expectedMetadata, $methodArguments->getMetadata());
    }

    /**
     * @return array[]
     */
    public function createDataProvider(): array
    {
        return [
            'empty, inline' => [
                'arguments' => [],
                'format' => MethodArguments::FORMAT_INLINE,
                'expectedMetadata' => new Metadata(),
            ],
            'empty, stacked' => [
                'arguments' => [],
                'format' => MethodArguments::FORMAT_STACKED,
                'expectedMetadata' => new Metadata(),
            ],
            'single argument' => [
                'arguments' => [
                    new LiteralExpression('1'),
                ],
                'format' => MethodArguments::FORMAT_INLINE,
                'expectedMetadata' => new Metadata(),
            ],
            'multiple arguments' => [
                'arguments' => [
                    new LiteralExpression('2'),
                    new LiteralExpression("\\'single-quoted value\\'"),
                    new LiteralExpression('"double-quoted value"'),
                ],
                'format' => MethodArguments::FORMAT_INLINE,
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'arguments' => [
                    new StaticObjectMethodInvocation(
                        new StaticObject(ClassName::class),
                        'staticMethodName'
                    )
                ],
                'format' => MethodArguments::FORMAT_INLINE,
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
    public function testRender(MethodArguments $arguments, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $arguments);
    }

    /**
     * @return array[]
     */
    public function renderDataProvider(): array
    {
        return [
            'empty, inline' => [
                'arguments' => new MethodArguments([]),
                'expectedString' => '',
            ],
            'empty, stacked' => [
                'arguments' => new MethodArguments([], MethodArguments::FORMAT_STACKED),
                'expectedString' => '',
            ],
            'has arguments, inline' => [
                'arguments' => new MethodArguments([
                    new LiteralExpression('1'),
                    new LiteralExpression("\\'single-quoted value\\'"),
                ]),
                'expectedString' => "1, \\'single-quoted value\\'",
            ],
            'has arguments, stacked' => [
                'arguments' => new MethodArguments(
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\\'single-quoted value\\'"),
                    ],
                    MethodArguments::FORMAT_STACKED
                ),
                'expectedString' => "\n" .
                    "    1,\n" .
                    "    \\'single-quoted value\\'\n",
            ],
            'indent stacked multi-line arguments' => [
                'arguments' => new MethodArguments(
                    [
                        new ObjectMethodInvocation(
                            new VariableDependency('NAVIGATOR'),
                            'find',
                            new MethodArguments([
                                new StaticObjectMethodInvocation(
                                    new StaticObject(ObjectMethodInvocation::class),
                                    'fromJson',
                                    new MethodArguments([
                                        new LiteralExpression(
                                            '{' . "\n" . '    "locator": ".selector"' . "\n" . '}'
                                        ),
                                    ])
                                )
                            ])
                        ),
                        new ClosureExpression(
                            new Body([
                                new Statement(
                                    new AssignmentExpression(
                                        new VariableName('variable'),
                                        new LiteralExpression('100')
                                    )
                                ),
                                new EmptyLine(),
                                new Statement(
                                    new ReturnExpression(
                                        new VariableName('variable'),
                                    )
                                ),
                            ])
                        ),
                    ],
                    MethodArguments::FORMAT_STACKED
                ),
                'expectedString' => "\n" .
                    '    {{ NAVIGATOR }}->find(ObjectMethodInvocation::fromJson({' . "\n" .
                    '        "locator": ".selector"' . "\n" .
                    '    })),' . "\n" .
                    '    (function () {' . "\n" .
                    '        $variable = 100;' . "\n" .
                    "\n" .
                    '        return $variable;' . "\n" .
                    '    })()' . "\n",
            ],
            'single array expression, pair contains inner array' => [
                'arguments' => new MethodArguments(
                    [
                        ArrayExpression::fromArray([
                            'name' => new ObjectMethodInvocation(
                                new VariableDependency('DEPENDENCY'),
                                'dataName'
                            ),
                            'data' => [
                                'key1' => 'value1',
                                'key2' => 'value2',
                            ],
                        ])
                    ]
                ),
                'expectedString' => "[\n" .
                    "    'name' => {{ DEPENDENCY }}->dataName(),\n" .
                    "    'data' => [\n" .
                    "        'key1' => 'value1',\n" .
                    "        'key2' => 'value2',\n" .
                    "    ],\n" .
                    ']',
            ],
        ];
    }
}
