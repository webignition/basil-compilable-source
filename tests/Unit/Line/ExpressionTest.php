<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\Expression;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class ExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(LineInterface $line, ?MetadataInterface $metadata, MetadataInterface $expectedMetadata)
    {
        $expression = new Expression($line, $metadata);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no metadata' => [
                'line' => new EmptyLine(),
                'metadata' => null,
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'line' => new VariablePlaceholder('NAME'),
                'metadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::create([
                        'NAME',
                    ])
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::create([
                        'NAME',
                    ])
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ExpressionInterface $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'variable placeholder' => [
                'expression' => new Expression(
                    new VariablePlaceholder('NAME'),
                    new Metadata([
                        Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::create([
                            'NAME',
                        ])
                    ])
                ),
                'expectedString' => '{{ NAME }}',
            ]
        ];
    }
}
