<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\ArrayExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;

class ArrayExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param array<mixed> $data
     */
    public function testCreate(array $data)
    {
        $expression = new ArrayExpression($data);

        $this->assertSame($data, $expression->getData());
        $this->assertEquals(new Metadata(), $expression->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'data' => [],
            ],
            'non-empty' => [
                'data' => [
                    'set1' => [
                        'x' => '5',
                        'y' => '\'string\''
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ArrayExpression $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'expression' => new ArrayExpression([]),
                'expectedString' => '[]',
            ],
            'single data set with single key:value numerical name' => [
                'expression' => new ArrayExpression([
                    0 => [
                        'key1' => 'value1',
                    ]
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
                    'data-set-one' => [
                        'key1' => 'value1',
                    ],
                ]),
                'expectedString' =>
                    "[\n" .
                    "    'data-set-one' => [\n" .
                    "        'key1' => 'value1',\n" .
                    "    ],\n" .
                    "]",
            ],
            'single data set with single key:value string name containing single quotes' => [
                'expression' => new ArrayExpression([
                    "\'data-set-one\'" => [
                        "\'key1\'" => "\'value1\'",
                    ],
                ]),
                'expectedString' =>
                    "[\n" .
                    "    '\'data-set-one\'' => [\n" .
                    "        '\'key1\'' => '\'value1\'',\n" .
                    "    ],\n" .
                    "]",
            ],
            'single data set with multiple key:value numerical name' => [
                'expression' => new ArrayExpression([
                    '0' => [
                        'key1' => 'value1',
                        'key2' => 'value2',
                    ],
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
                    '0' => [
                        'key1' => 'value1',
                        'key2' => 'value2',
                    ],
                    '1' => [
                        'key1' => 'value3',
                        'key2' => 'value4',
                    ],
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
        ];
    }
}
