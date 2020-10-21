<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ArrayExpression;
use webignition\BasilCompilableSource\Expression\ArrayFoo;
use webignition\BasilCompilableSource\Expression\ArrayKey;
use webignition\BasilCompilableSource\Expression\ArrayPair;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;

class ArrayFooTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ArrayFoo $foo, MetadataInterface $expectedMetadata)
    {
        self::assertEquals($expectedMetadata, $foo->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'foo' => new ArrayFoo(
                    'identifier1',
                    []
                ),
                'expectedMetadata' => new Metadata(),
            ],
            'no metadata' => [
                'foo' => new ArrayFoo(
                    'identifier1-',
                    [
                        new ArrayPair(
                            new ArrayKey('key1'),
                            new LiteralExpression('\'value1\'')
                        ),
                    ]
                ),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'foo' => new ArrayFoo(
                    'identifier1-',
                    [
                        new ArrayPair(
                            new ArrayKey('key3'),
                            new ObjectMethodInvocation(
                                new VariableDependency('OBJECT'),
                                'methodName'
                            )
                        ),
                    ]
                ),
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
    public function testRender(ArrayFoo $foo, string $expectedString)
    {
        $this->assertSame($expectedString, $foo->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'foo' => new ArrayFoo(
                    'identifier1',
                    []
                ),
                'expectedString' => '[]',
            ],
            'single pair' => [
                'foo' => new ArrayFoo(
                    'identifier1-',
                    [
                        new ArrayPair(
                            new ArrayKey('key1'),
                            new LiteralExpression('\'value1\'')
                        ),
                    ]
                ),
                'expectedString' =>
                    "[\n" .
                    "    'key1' => 'value1',\n" .
                    "]",
            ],
            'multiple pairs' => [
                'foo' => new ArrayFoo(
                    'identifier1-',
                    [
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
                    ]
                ),
                'expectedString' =>
                    "[\n" .
                    "    'key1' => 'value1',\n" .
                    "    'key2' => \$variableName,\n" .
                    "    'key3' => {{ OBJECT }}->methodName(),\n" .
                    "]",
            ],
//            'single data set with single key:value numerical name' => [
//                'foo' => new ArrayExpression([
//                    0 => [
//                        'key1' => 'value1',
//                    ]
//                ]),
//                'expectedString' =>
//                    "[\n" .
//                    "    '0' => [\n" .
//                    "        'key1' => 'value1',\n" .
//                    "    ],\n" .
//                    "]",
//            ],
//            'single data set with single key:value string name' => [
//                'foo' => new ArrayExpression([
//                    'data-set-one' => [
//                        'key1' => 'value1',
//                    ],
//                ]),
//                'expectedString' =>
//                    "[\n" .
//                    "    'data-set-one' => [\n" .
//                    "        'key1' => 'value1',\n" .
//                    "    ],\n" .
//                    "]",
//            ],
//            'single data set with single key:value string name containing single quotes' => [
//                'foo' => new ArrayExpression([
//                    "\'data-set-one\'" => [
//                        "\'key1\'" => "\'value1\'",
//                    ],
//                ]),
//                'expectedString' =>
//                    "[\n" .
//                    "    '\'data-set-one\'' => [\n" .
//                    "        '\'key1\'' => '\'value1\'',\n" .
//                    "    ],\n" .
//                    "]",
//            ],
//            'single data set with multiple key:value numerical name' => [
//                'foo' => new ArrayExpression([
//                    '0' => [
//                        'key1' => 'value1',
//                        'key2' => 'value2',
//                    ],
//                ]),
//                'expectedString' =>
//                    "[\n" .
//                    "    '0' => [\n" .
//                    "        'key1' => 'value1',\n" .
//                    "        'key2' => 'value2',\n" .
//                    "    ],\n" .
//                    "]",
//            ],
//            'multiple data sets with multiple key:value numerical name' => [
//                'foo' => new ArrayExpression([
//                    '0' => [
//                        'key1' => 'value1',
//                        'key2' => 'value2',
//                    ],
//                    '1' => [
//                        'key1' => 'value3',
//                        'key2' => 'value4',
//                    ],
//                ]),
//                'expectedString' =>
//                    "[\n" .
//                    "    '0' => [\n" .
//                    "        'key1' => 'value1',\n" .
//                    "        'key2' => 'value2',\n" .
//                    "    ],\n" .
//                    "    '1' => [\n" .
//                    "        'key1' => 'value3',\n" .
//                    "        'key2' => 'value4',\n" .
//                    "    ],\n" .
//                    "]",
//            ],
//            'single data set with VariableName value' => [
//                'foo' => new ArrayExpression([
//                    'data-set-one' => [
//                        'key1' => new VariableName('variableName'),
//                    ],
//                ]),
//                'expectedString' =>
//                    "[\n" .
//                    "    'data-set-one' => [\n" .
//                    "        'key1' => \$variableName,\n" .
//                    "    ],\n" .
//                    "]",
//            ],
//            'single data set with ObjectMethodInvocation value' => [
//                'foo' => new ArrayExpression([
//                    'data-set-one' => [
//                        'key1' => new ObjectMethodInvocation(
//                            new VariableDependency('OBJECT'),
//                            'methodName'
//                        ),
//                    ],
//                ]),
//                'expectedString' =>
//                    "[\n" .
//                    "    'data-set-one' => [\n" .
//                    "        'key1' => {{ OBJECT }}->methodName(),\n" .
//                    "    ],\n" .
//                    "]",
//            ],
        ];
    }
}
