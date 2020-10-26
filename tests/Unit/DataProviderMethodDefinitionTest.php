<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\DataProviderMethodDefinition;
use webignition\BasilCompilableSource\DataProviderMethodDefinitionInterface;
use webignition\BasilCompilableSource\MethodDefinition;

class DataProviderMethodDefinitionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $name
     * @param array<mixed> $data
     */
    public function testCreate(string $name, array $data)
    {
        $methodDefinition = new DataProviderMethodDefinition($name, $data);

        $this->assertSame($name, $methodDefinition->getName());
        $this->assertSame([], $methodDefinition->getArguments());
        $this->assertsame(MethodDefinition::VISIBILITY_PUBLIC, $methodDefinition->getVisibility());
        $this->assertSame('array', $methodDefinition->getReturnType());
        $this->assertFalse($methodDefinition->isStatic());
        $this->assertNull($methodDefinition->getDocBlock());
        $this->assertSame($data, $methodDefinition->getData());
    }

    public function createDataProvider(): array
    {
        return [
            'empty data' => [
                'name' => 'emptyData',
                'data' => [],
            ],
            'non-empty data' => [
                'name' => 'nonEmptyData',
                'data' => [
                    0 => [
                        'x' => '1',
                        'y' => '\'string1\'',
                    ],
                    1 => [
                        'x' => '2',
                        'y' => '\'string2\'',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(DataProviderMethodDefinitionInterface $methodDefinition, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $methodDefinition);
    }

    public function renderDataProvider(): array
    {
        return [
            'empty data' => [
                'methodDefinition' => new DataProviderMethodDefinition('emptyDataDataProvider', []),
                'expectedString' =>
                    'public function emptyDataDataProvider(): array' . "\n" .
                    '{' . "\n" .
                    '    return [];' . "\n" .
                    '}'
            ],
            'non-empty data' => [
                'methodDefinition' => new DataProviderMethodDefinition('emptyDataDataProvider', [
                    0 => [
                        'x' => '1',
                        'y' => "\'string1\'",
                    ],
                    1 => [
                        'x' => '2',
                        'y' => "\'string2\'",
                    ],
                ]),
                'expectedString' =>
                    "public function emptyDataDataProvider(): array\n" .
                    "{\n" .
                    "    return [\n" .
                    "        '0' => [\n" .
                    "            'x' => '1',\n" .
                    "            'y' => '\'string1\'',\n" .
                    "        ],\n" .
                    "        '1' => [\n" .
                    "            'x' => '2',\n" .
                    "            'y' => '\'string2\'',\n" .
                    "        ],\n" .
                    "    ];\n" .
                    "}"
            ],
        ];
    }
}
