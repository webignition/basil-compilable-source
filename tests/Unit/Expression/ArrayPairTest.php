<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ArrayExpression;
use webignition\BasilCompilableSource\Expression\ArrayKey;
use webignition\BasilCompilableSource\Expression\ArrayPair;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableName;

class ArrayPairTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ArrayPair $pair, string $expectedString)
    {
        $this->assertSame($expectedString, $pair->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty key, empty string value' => [
                'pair' => new ArrayPair(
                    new ArrayKey(''),
                    new LiteralExpression('\'\'')
                ),
                'expectedString' => "'' => '',",
            ],
            'empty key, string value' => [
                'pair' => new ArrayPair(
                    new ArrayKey(''),
                    new LiteralExpression('\'value\'')
                ),
                'expectedString' => "'' => 'value',",
            ],
            'empty key, integer value' => [
                'pair' => new ArrayPair(
                    new ArrayKey(''),
                    new LiteralExpression('2')
                ),
                'expectedString' => "'' => 2,",
            ],
            'string value' => [
                'pair' => new ArrayPair(
                    new ArrayKey('key'),
                    new LiteralExpression('\'value\'')
                ),
                'expectedString' => "'key' => 'value',",
            ],

//            'single data set with single key:value numerical name' => [
//                'pair' => new ArrayExpression([
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
//                'pair' => new ArrayExpression([
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
//                'pair' => new ArrayExpression([
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
//                'pair' => new ArrayExpression([
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
//                'pair' => new ArrayExpression([
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
//                'pair' => new ArrayExpression([
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
//                'pair' => new ArrayExpression([
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
