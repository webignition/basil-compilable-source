<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ArrayExpression;
use webignition\BasilCompilableSource\Expression\ArrayKey;
use webignition\BasilCompilableSource\Expression\ArrayPair;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;
use webignition\Stubble\DeciderFactory;
use webignition\Stubble\UnresolvedVariableFinder;
use webignition\Stubble\VariableResolver;
use webignition\StubbleResolvable\ResolvableInterface;

class ArrayExpressionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ArrayExpression $expression, MetadataInterface $expectedMetadata)
    {
        self::assertEquals($expectedMetadata, $expression->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'expression' => new ArrayExpression([]),
                'expectedMetadata' => new Metadata(),
            ],
            'no metadata' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('key1'),
                        new LiteralExpression('\'value1\'')
                    ),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('key3'),
                        new ObjectMethodInvocation(
                            new VariableDependency('OBJECT'),
                            'methodName'
                        )
                    ),
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'OBJECT',
                    ])
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ArrayExpression $expression, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'expression' => new ArrayExpression([]),
                'expectedString' => '[]',
            ],
            'single pair' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('key1'),
                        new LiteralExpression('\'value1\'')
                    ),
                ]),
                'expectedString' =>
                    "[\n" .
                    "    'key1' => 'value1',\n" .
                    "]",
            ],
            'multiple pairs' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('key1'),
                        new LiteralExpression('\'value1\'')
                    ),
                    new ArrayPair(
                        new ArrayKey('key2'),
                        new VariableName('variableName')
                    ),
                    new ArrayPair(
                        new ArrayKey('key3'),
                        new ObjectMethodInvocation(
                            new VariableDependency('OBJECT'),
                            'methodName'
                        )
                    ),
                ]),
                'expectedString' =>
                    "[\n" .
                    "    'key1' => 'value1',\n" .
                    "    'key2' => \$variableName,\n" .
                    "    'key3' => {{ OBJECT }}->methodName(),\n" .
                    "]",
            ],
            'single data set with single key:value numerical name' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('0'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value1\'')
                            ),
                        ])
                    ),
                ]),
                'expectedString' =>
                    "[\n" .
                    "    '0' => [\n" .
                    "        'key1' => 'value1',\n" .
                    "    ],\n" .
                    "]",
            ],
            'single data set with single key:value string name' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('data-set-one'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value1\'')
                            ),
                        ])
                    ),
                ]),
                'expectedString' =>
                    "[\n" .
                    "    'data-set-one' => [\n" .
                    "        'key1' => 'value1',\n" .
                    "    ],\n" .
                    "]",
            ],
            'single data set with multiple key:value numerical name' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('0'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value1\'')
                            ),
                            new ArrayPair(
                                new ArrayKey('key2'),
                                new LiteralExpression('\'value2\'')
                            ),
                        ])
                    ),
                ]),
                'expectedString' =>
                    "[\n" .
                    "    '0' => [\n" .
                    "        'key1' => 'value1',\n" .
                    "        'key2' => 'value2',\n" .
                    "    ],\n" .
                    "]",
            ],
            'multiple data sets with multiple key:value numerical name' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('0'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value1\'')
                            ),
                            new ArrayPair(
                                new ArrayKey('key2'),
                                new LiteralExpression('\'value2\'')
                            ),
                        ])
                    ),
                    new ArrayPair(
                        new ArrayKey('1'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value3\'')
                            ),
                            new ArrayPair(
                                new ArrayKey('key2'),
                                new LiteralExpression('\'value4\'')
                            ),
                        ])
                    ),
                ]),
                'expectedString' =>
                    "[\n" .
                    "    '0' => [\n" .
                    "        'key1' => 'value1',\n" .
                    "        'key2' => 'value2',\n" .
                    "    ],\n" .
                    "    '1' => [\n" .
                    "        'key1' => 'value3',\n" .
                    "        'key2' => 'value4',\n" .
                    "    ],\n" .
                    "]",
            ],

            'single data set with VariableName value' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('data-set-one'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new VariableName('variableName')
                            ),
                        ])
                    ),
                ]),
                'expectedString' =>
                    "[\n" .
                    "    'data-set-one' => [\n" .
                    "        'key1' => \$variableName,\n" .
                    "    ],\n" .
                    "]",
            ],
            'single data set with ObjectMethodInvocation value' => [
                'expression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('data-set-one'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new ObjectMethodInvocation(
                                    new VariableDependency('OBJECT'),
                                    'methodName'
                                )
                            ),
                        ])
                    ),
                ]),
                'expectedString' =>
                    "[\n" .
                    "    'data-set-one' => [\n" .
                    "        'key1' => {{ OBJECT }}->methodName(),\n" .
                    "    ],\n" .
                    "]",
            ],
        ];
    }

    /**
     * @dataProvider fromDataSetsDataProvider
     */
    public function testFromDataSets(ArrayExpression $expression, ArrayExpression $expectedExpression)
    {
        self::assertEquals($expectedExpression, $expression);
    }

    public function fromDataSetsDataProvider(): array
    {
        return [
            'empty' => [
                'expression' => ArrayExpression::fromDataSets([]),
                'expectedExpression' => new ArrayExpression([]),
            ],
            'single data set with single key:value numerical name' => [
                'expression' => ArrayExpression::fromDataSets([
                    0 => [
                        'key1' => 'value1',
                    ],
                ]),
                'expectedExpression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('0'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value1\'')
                            ),
                        ])
                    ),
                ]),
            ],
            'single data set with single key:value string name' => [
                'expression' => ArrayExpression::fromDataSets([
                    'data-set-one' => [
                        'key1' => 'value1',
                    ],
                ]),
                'expectedExpression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('data-set-one'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value1\'')
                            ),
                        ])
                    ),
                ]),
            ],
            'single data set with multiple key:value numerical name' => [
                'expression' => ArrayExpression::fromDataSets([
                    0 => [
                        'key1' => 'value1',
                        'key2' => 'value2',
                    ],
                ]),
                'expectedExpression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('0'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value1\'')
                            ),
                            new ArrayPair(
                                new ArrayKey('key2'),
                                new LiteralExpression('\'value2\'')
                            ),
                        ])
                    ),
                ]),
            ],
            'multiple data sets with multiple key:value numerical name' => [
                'expression' => ArrayExpression::fromDataSets([
                    0 => [
                        'key1' => 'value1',
                        'key2' => 'value2',
                    ],
                    1 => [
                        'key1' => 'value3',
                        'key2' => 'value4',
                    ],
                ]),
                'expectedExpression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('0'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value1\'')
                            ),
                            new ArrayPair(
                                new ArrayKey('key2'),
                                new LiteralExpression('\'value2\'')
                            ),
                        ])
                    ),
                    new ArrayPair(
                        new ArrayKey('1'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new LiteralExpression('\'value3\'')
                            ),
                            new ArrayPair(
                                new ArrayKey('key2'),
                                new LiteralExpression('\'value4\'')
                            ),
                        ])
                    ),
                ]),
            ],
            'single data set with VariableName value' => [
                'expression' => ArrayExpression::fromDataSets([
                    'data-set-one' => [
                        'key1' => new VariableName('variableName'),
                    ],
                ]),
                'expectedExpression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('data-set-one'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new VariableName('variableName')
                            ),
                        ])
                    ),
                ]),
            ],
            'single data set with ObjectMethodInvocation value' => [
                'expression' => ArrayExpression::fromDataSets([
                    'data-set-one' => [
                        'key1' => new ObjectMethodInvocation(
                            new VariableDependency('OBJECT'),
                            'methodName'
                        ),
                    ],
                ]),
                'expectedExpression' => new ArrayExpression([
                    new ArrayPair(
                        new ArrayKey('data-set-one'),
                        new ArrayExpression([
                            new ArrayPair(
                                new ArrayKey('key1'),
                                new ObjectMethodInvocation(
                                    new VariableDependency('OBJECT'),
                                    'methodName'
                                )
                            ),
                        ])
                    ),
                ]),
            ],
        ];
    }
}
