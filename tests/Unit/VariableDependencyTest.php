<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class VariableDependencyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name, string $type, string $expectedType)
    {
        $dependency = new VariableDependency($name, $type);

        $this->assertSame($name, $dependency->getName());
        $this->assertSame($expectedType, $dependency->getType());
    }

    public function constructDataProvider(): array
    {
        return [
            'dependency' => [
                'name' => 'DEPENDENCY',
                'type' => VariableDependency::TYPE_DEPENDENCY,
                'expectedType' => VariableDependency::TYPE_DEPENDENCY,
            ],
            'export' => [
                'name' => 'EXPORT',
                'type' => VariableDependency::TYPE_EXPORT,
                'expectedType' => VariableDependency::TYPE_EXPORT,
            ],
            'invalid type' => [
                'name' => 'EXPORT?',
                'type' => 'invalid',
                'expectedType' => VariableDependency::TYPE_EXPORT,
            ],
        ];
    }

    public function testCreateDependency()
    {
        $dependency = VariableDependency::createDependency('DEPENDENCY');

        $this->assertSame(VariableDependency::TYPE_DEPENDENCY, $dependency->getType());
    }

    public function testCreateExport()
    {
        $dependency = VariableDependency::createExport('EXPORT');

        $this->assertSame(VariableDependency::TYPE_EXPORT, $dependency->getType());
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(VariableDependency $dependency, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $dependency->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'variable dependency' => [
                'placeholder' => VariableDependency::createDependency('DEPENDENCY'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariableDependencyCollection::create(
                        VariableDependency::TYPE_DEPENDENCY,
                        [
                            'DEPENDENCY',
                        ]
                    ),
                ]),
            ],
            'variable export' => [
                'placeholder' => VariableDependency::createExport('EXPORT'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariableDependencyCollection::create(
                        VariableDependency::TYPE_EXPORT,
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
    public function testRender(VariableDependency $dependency, string $expectedString)
    {
        $this->assertSame($expectedString, $dependency->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'placeholder' => new VariableDependency('', VariableDependency::TYPE_EXPORT),
                'expectedString' => '{{  }}',
            ],
            'non-empty' => [
                'placeholder' => new VariableDependency('NAME', VariableDependency::TYPE_EXPORT),
                'expectedString' => '{{ NAME }}',
            ],
        ];
    }
}
