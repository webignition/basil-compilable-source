<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class VariablePlaceholderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name, string $type, string $expectedType)
    {
        $placeholder = new VariablePlaceholder($name, $type);

        $this->assertSame($name, $placeholder->getName());
        $this->assertSame($expectedType, $placeholder->getType());
    }

    public function constructDataProvider(): array
    {
        return [
            'dependency' => [
                'name' => 'DEPENDENCY',
                'type' => VariablePlaceholder::TYPE_DEPENDENCY,
                'expectedType' => VariablePlaceholder::TYPE_DEPENDENCY,
            ],
            'export' => [
                'name' => 'EXPORT',
                'type' => VariablePlaceholder::TYPE_EXPORT,
                'expectedType' => VariablePlaceholder::TYPE_EXPORT,
            ],
            'invalid type' => [
                'name' => 'EXPORT?',
                'type' => 'invalid',
                'expectedType' => VariablePlaceholder::TYPE_EXPORT,
            ],
        ];
    }

    public function testCreateDependency()
    {
        $placeholder = VariablePlaceholder::createDependency('DEPENDENCY');

        $this->assertSame(VariablePlaceholder::TYPE_DEPENDENCY, $placeholder->getType());
    }

    public function testCreateExport()
    {
        $placeholder = VariablePlaceholder::createExport('EXPORT');

        $this->assertSame(VariablePlaceholder::TYPE_EXPORT, $placeholder->getType());
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(VariablePlaceholder $placeholder, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $placeholder->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'variable dependency' => [
                'placeholder' => VariablePlaceholder::createDependency('DEPENDENCY'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::create(
                        VariablePlaceholder::TYPE_DEPENDENCY,
                        [
                            'DEPENDENCY',
                        ]
                    ),
                ]),
            ],
            'variable export' => [
                'placeholder' => VariablePlaceholder::createExport('EXPORT'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::create(
                        VariablePlaceholder::TYPE_EXPORT,
                        [
                            'EXPORT',
                        ]
                    ),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(VariablePlaceholder $placeholder, string $expectedString)
    {
        $this->assertSame($expectedString, $placeholder->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'placeholder' => new VariablePlaceholder('', VariablePlaceholder::TYPE_EXPORT),
                'expectedString' => '{{  }}',
            ],
            'non-empty' => [
                'placeholder' => new VariablePlaceholder('NAME', VariablePlaceholder::TYPE_EXPORT),
                'expectedString' => '{{ NAME }}',
            ],
        ];
    }
}
