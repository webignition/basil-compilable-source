<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodArguments;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\EmptyLine;
use webignition\BasilCompilableSource\Expression\AssignmentExpression;
use webignition\BasilCompilableSource\Expression\ClosureExpression;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Expression\ReturnExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\FooMethodArguments;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableName;

class FooMethodArgumentsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param ExpressionInterface[] $arguments
     * @param MetadataInterface $expectedMetadata
     */
    public function testCreate(
        array $arguments,
        string $format,
        MetadataInterface $expectedMetadata
    ) {
        $methodArguments = new FooMethodArguments($arguments, $format);

        $this->assertSame($arguments, $methodArguments->getArguments());
        $this->assertSame($format, $methodArguments->getFormat());
        $this->assertEquals($expectedMetadata, $methodArguments->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'empty, inline' => [
                'arguments' => [],
                'format' => FooMethodArguments::FORMAT_INLINE,
                'expectedMetadata' => new Metadata(),
            ],
            'empty, stacked' => [
                'arguments' => [],
                'format' => FooMethodArguments::FORMAT_STACKED,
                'expectedMetadata' => new Metadata(),
            ],
            'single argument' => [
                'arguments' => [
                    new LiteralExpression('1'),
                ],
                'format' => FooMethodArguments::FORMAT_INLINE,
                'expectedMetadata' => new Metadata(),
            ],
            'multiple arguments' => [
                'arguments' => [
                    new LiteralExpression('2'),
                    new LiteralExpression("\'single-quoted value\'"),
                    new LiteralExpression('"double-quoted value"'),
                ],
                'format' => FooMethodArguments::FORMAT_INLINE,
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'arguments' => [
                    new StaticObjectMethodInvocation(
                        new StaticObject(ClassName::class),
                        'staticMethodName'
                    )
                ],
                'format' => FooMethodArguments::FORMAT_INLINE,
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
    public function testRender(FooMethodArguments $arguments, string $expectedString)
    {
        $this->assertSame($expectedString, $arguments->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty, inline' => [
                'arguments' => new FooMethodArguments([]),
                'expectedString' => '',
            ],
            'empty, stacked' => [
                'arguments' => new FooMethodArguments([], FooMethodArguments::FORMAT_STACKED),
                'expectedString' => '',
            ],
            'has arguments, inline' => [
                'arguments' => new FooMethodArguments([
                    new LiteralExpression('1'),
                    new LiteralExpression("\'single-quoted value\'"),
                ]),
                'expectedString' => "1, \'single-quoted value\'",
            ],
            'has arguments, stacked' => [
                'arguments' => new FooMethodArguments(
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ],
                    FooMethodArguments::FORMAT_STACKED
                ),
                'expectedString' => "\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n",
            ],
            'indent stacked multi-line arguments' => [
                'arguments' => new FooMethodArguments(
                    [
                        new ObjectMethodInvocation(
                            new VariableDependency('NAVIGATOR'),
                            'find',
                            new FooMethodArguments([
                                new StaticObjectMethodInvocation(
                                    new StaticObject(ObjectMethodInvocation::class),
                                    'fromJson',
                                    new FooMethodArguments([
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
                    FooMethodArguments::FORMAT_STACKED
                ),
                'expectedString' =>
                    "\n" .
                    '    {{ NAVIGATOR }}->find(ObjectMethodInvocation::fromJson({' . "\n" .
                    '        "locator": ".selector"' . "\n" .
                    '    })),' . "\n" .
                    '    (function () {' . "\n" .
                    '        $variable = 100;' . "\n" .
                    "\n" .
                    '        return $variable;' . "\n" .
                    '    })()' . "\n",
            ],
        ];
    }
}
