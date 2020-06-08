<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Expression\ClassDependency;
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
     */
    public function testCreate(
        array $components,
        ClassDependencyCollection $expectedClassDependencies,
        VariableDependencyCollection $expectedVariableDependencies
    ) {
        $metadata = new Metadata($components);

        $this->assertEquals($expectedClassDependencies, $metadata->getClassDependencies());
        $this->assertEquals($expectedVariableDependencies, $metadata->getVariableDependencies());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'components' => [],
                'expectedClassDependencies' => new ClassDependencyCollection(),
                'expectedVariableDependencies' => new VariableDependencyCollection(),
            ],
            'components set, incorrect types' => [
                'components' => [
                    Metadata::KEY_CLASS_DEPENDENCIES => 'string',
                    Metadata::KEY_VARIABLE_DEPENDENCIES => 'string',
                ],
                'expectedClassDependencies' => new ClassDependencyCollection(),
                'expectedVariableDependencies' => new VariableDependencyCollection(),
            ],
            'components set, correct types' => [
                'components' => [
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassDependency(ClassDependency::class),
                    ]),
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'VARIABLE_DEPENDENCY',
                    ]),
                ],
                'expectedClassDependencies' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                ]),
                'expectedVariableDependencies' => new VariableDependencyCollection([
                    'VARIABLE_DEPENDENCY',
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
            Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                'VARIABLE_DEPENDENCY_1',
                'VARIABLE_DEPENDENCY_2',
            ]),
        ]);

        $metadata2 = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassDependency(ClassDependency::class),
                new ClassDependency(Metadata::class),
            ]),
            Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                'VARIABLE_DEPENDENCY_2',
                'VARIABLE_DEPENDENCY_3',
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
                Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                    'VARIABLE_DEPENDENCY_1',
                    'VARIABLE_DEPENDENCY_2',
                    'VARIABLE_DEPENDENCY_3',
                ]),
            ])
        );
    }
}
