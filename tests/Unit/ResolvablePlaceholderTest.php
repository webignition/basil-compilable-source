<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\ResolvablePlaceholder;
use webignition\BasilCompilableSource\ResolvablePlaceholderCollection;

class ResolvablePlaceholderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name, string $type, string $expectedType)
    {
        $placeholder = new ResolvablePlaceholder($name, $type);

        $this->assertSame($name, $placeholder->getName());
        $this->assertSame($expectedType, $placeholder->getType());
    }

    public function constructDataProvider(): array
    {
        return [
            'dependency' => [
                'name' => 'DEPENDENCY',
                'type' => ResolvablePlaceholder::TYPE_DEPENDENCY,
                'expectedType' => ResolvablePlaceholder::TYPE_DEPENDENCY,
            ],
            'export' => [
                'name' => 'EXPORT',
                'type' => ResolvablePlaceholder::TYPE_EXPORT,
                'expectedType' => ResolvablePlaceholder::TYPE_EXPORT,
            ],
            'invalid type' => [
                'name' => 'EXPORT?',
                'type' => 'invalid',
                'expectedType' => ResolvablePlaceholder::TYPE_EXPORT,
            ],
        ];
    }

    public function testCreateDependency()
    {
        $placeholder = ResolvablePlaceholder::createDependency('DEPENDENCY');

        $this->assertSame(ResolvablePlaceholder::TYPE_DEPENDENCY, $placeholder->getType());
    }

    public function testCreateExport()
    {
        $placeholder = ResolvablePlaceholder::createExport('EXPORT');

        $this->assertSame(ResolvablePlaceholder::TYPE_EXPORT, $placeholder->getType());
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ResolvablePlaceholder $placeholder, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $placeholder->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'variable dependency' => [
                'placeholder' => ResolvablePlaceholder::createDependency('DEPENDENCY'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => ResolvablePlaceholderCollection::create(
                        ResolvablePlaceholder::TYPE_DEPENDENCY,
                        [
                            'DEPENDENCY',
                        ]
                    ),
                ]),
            ],
            'variable export' => [
                'placeholder' => ResolvablePlaceholder::createExport('EXPORT'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => ResolvablePlaceholderCollection::create(
                        ResolvablePlaceholder::TYPE_EXPORT,
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
    public function testRender(ResolvablePlaceholder $placeholder, string $expectedString)
    {
        $this->assertSame($expectedString, $placeholder->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'placeholder' => new ResolvablePlaceholder('', ResolvablePlaceholder::TYPE_EXPORT),
                'expectedString' => '{{  }}',
            ],
            'non-empty' => [
                'placeholder' => new ResolvablePlaceholder('NAME', ResolvablePlaceholder::TYPE_EXPORT),
                'expectedString' => '{{ NAME }}',
            ],
        ];
    }
}
