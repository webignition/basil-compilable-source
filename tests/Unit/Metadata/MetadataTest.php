<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class MetadataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param array<mixed> $components
     * @param ClassDependencyCollection $expectedClassDependencies
     * @param VariableDependencyCollection $expectedVariableDependencies
     * @param VariableDependencyCollection $expectedVariableExports
     */
    public function testCreate(
        array $components,
        ClassDependencyCollection $expectedClassDependencies,
        VariableDependencyCollection $expectedVariableDependencies,
        VariableDependencyCollection $expectedVariableExports
    ) {
        $metadata = new Metadata($components);

        $this->assertEquals($expectedClassDependencies, $metadata->getClassDependencies());
        $this->assertEquals($expectedVariableDependencies, $metadata->getVariableDependencies());
        $this->assertEquals($expectedVariableExports, $metadata->getVariableExports());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'components' => [],
                'expectedClassDependencies' => new ClassDependencyCollection(),
                'expectedVariableDependencies' => VariableDependencyCollection::createDependencyCollection(),
                'expectedVariableExports' => VariableDependencyCollection::createExportCollection(),
            ],
            'components set, incorrect types' => [
                'components' => [
                    Metadata::KEY_CLASS_DEPENDENCIES => 'string',
                    Metadata::KEY_VARIABLE_DEPENDENCIES => 'string',
                    Metadata::KEY_VARIABLE_EXPORTS => 'string',
                ],
                'expectedClassDependencies' => new ClassDependencyCollection(),
                'expectedVariableDependencies' => VariableDependencyCollection::createDependencyCollection(),
                'expectedVariableExports' => VariableDependencyCollection::createExportCollection(),
            ],
            'components set, correct types' => [
                'components' => [
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassDependency(ClassDependency::class),
                    ]),
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariableDependencyCollection::createDependencyCollection([
                        'VARIABLE_DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => VariableDependencyCollection::createExportCollection([
                        'VARIABLE_EXPORT',
                    ]),
                ],
                'expectedClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                ]),
                'expectedVariableDependencies' => VariableDependencyCollection::createDependencyCollection([
                    'VARIABLE_DEPENDENCY',
                ]),
                'expectedVariableExports' => VariableDependencyCollection::createExportCollection([
                    'VARIABLE_EXPORT',
                ]),
            ],
        ];
    }

    public function testMerge()
    {
        $metadata1 = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassDependency(ClassDependency::class),
            ]),
            Metadata::KEY_VARIABLE_DEPENDENCIES => VariableDependencyCollection::createDependencyCollection([
                'VARIABLE_DEPENDENCY_1',
                'VARIABLE_DEPENDENCY_2',
            ]),
            Metadata::KEY_VARIABLE_EXPORTS => VariableDependencyCollection::createExportCollection([
                'VARIABLE_EXPORT_1',
                'VARIABLE_EXPORT_2',
            ]),
        ]);

        $metadata2 = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassDependency(ClassDependency::class),
                new ClassDependency(Metadata::class),
            ]),
            Metadata::KEY_VARIABLE_DEPENDENCIES => VariableDependencyCollection::createDependencyCollection([
                'VARIABLE_DEPENDENCY_2',
                'VARIABLE_DEPENDENCY_3',
            ]),
            Metadata::KEY_VARIABLE_EXPORTS => VariableDependencyCollection::createExportCollection([
                'VARIABLE_EXPORT_2',
                'VARIABLE_EXPORT_3',
            ]),
        ]);

        $metadata = $metadata1->merge($metadata2);

        $this->assertEquals(
            $metadata,
            new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                    new ClassDependency(Metadata::class),
                ]),
                Metadata::KEY_VARIABLE_DEPENDENCIES => VariableDependencyCollection::createDependencyCollection([
                    'VARIABLE_DEPENDENCY_1',
                    'VARIABLE_DEPENDENCY_2',
                    'VARIABLE_DEPENDENCY_3',
                ]),
                Metadata::KEY_VARIABLE_EXPORTS => VariableDependencyCollection::createExportCollection([
                    'VARIABLE_EXPORT_1',
                    'VARIABLE_EXPORT_2',
                    'VARIABLE_EXPORT_3',
                ]),
            ])
        );
    }
}
