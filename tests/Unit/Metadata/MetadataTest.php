<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class MetadataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param array<mixed> $components
     * @param ClassDependencyCollection $expectedClassDependencies
     * @param VariablePlaceholderCollection $expectedVariableDependencies
     * @param VariablePlaceholderCollection $expectedVariableExports
     */
    public function testCreate(
        array $components,
        ClassDependencyCollection $expectedClassDependencies,
        VariablePlaceholderCollection $expectedVariableDependencies,
        VariablePlaceholderCollection $expectedVariableExports
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
                'expectedVariableDependencies' => new VariablePlaceholderCollection(),
                'expectedVariableExports' => new VariablePlaceholderCollection(),
            ],
            'components set, incorrect types' => [
                'components' => [
                    Metadata::KEY_CLASS_DEPENDENCIES => 'string',
                    Metadata::KEY_VARIABLE_DEPENDENCIES => 'string',
                    Metadata::KEY_VARIABLE_EXPORTS => 'string',
                ],
                'expectedClassDependencies' => new ClassDependencyCollection(),
                'expectedVariableDependencies' => new VariablePlaceholderCollection(),
                'expectedVariableExports' => new VariablePlaceholderCollection(),
            ],
            'components set, correct types' => [
                'components' => [
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassDependency(ClassDependency::class),
                    ]),
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::create([
                        'VARIABLE_DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::create([
                        'VARIABLE_EXPORT',
                    ]),
                ],
                'expectedClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                ]),
                'expectedVariableDependencies' => VariablePlaceholderCollection::create([
                    'VARIABLE_DEPENDENCY',
                ]),
                'expectedVariableExports' => VariablePlaceholderCollection::create([
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
            Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::create([
                'VARIABLE_DEPENDENCY_1',
                'VARIABLE_DEPENDENCY_2',
            ]),
            Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::create([
                'VARIABLE_EXPORT_1',
                'VARIABLE_EXPORT_2',
            ]),
        ]);

        $metadata2 = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassDependency(ClassDependency::class),
                new ClassDependency(Metadata::class),
            ]),
            Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::create([
                'VARIABLE_DEPENDENCY_2',
                'VARIABLE_DEPENDENCY_3',
            ]),
            Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::create([
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
                Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::create([
                    'VARIABLE_DEPENDENCY_1',
                    'VARIABLE_DEPENDENCY_2',
                    'VARIABLE_DEPENDENCY_3',
                ]),
                Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::create([
                    'VARIABLE_EXPORT_1',
                    'VARIABLE_EXPORT_2',
                    'VARIABLE_EXPORT_3',
                ]),
            ])
        );
    }
}
