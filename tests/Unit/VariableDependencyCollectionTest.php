<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class VariableDependencyCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $type
     * @param string[] $names
     * @param VariableDependency[] $expectedPlaceholders
     */
    public function testCreate(string $type, array $names, array $expectedPlaceholders)
    {
        $collection = VariableDependencyCollection::create($type, $names);

        $this->assertCount(count($expectedPlaceholders), $collection);

        $this->assertEquals($expectedPlaceholders, $this->getCollectionVariablePlaceholders($collection));
    }

    public function createDataProvider(): array
    {
        return [
            'dependency collection' => [
                'type' => VariableDependency::TYPE_DEPENDENCY,
                'names' => [
                    'DEPENDENCY_1',
                    'DEPENDENCY_2',
                    'DEPENDENCY_2',
                    'DEPENDENCY_3',
                ],
                'expectedPlaceholders' => [
                    'DEPENDENCY_1' => VariableDependency::createDependency('DEPENDENCY_1'),
                    'DEPENDENCY_2' => VariableDependency::createDependency('DEPENDENCY_2'),
                    'DEPENDENCY_3' => VariableDependency::createDependency('DEPENDENCY_3'),
                ],
            ],
            'export collection' => [
                'type' => VariableDependency::TYPE_EXPORT,
                'names' => [
                    'EXPORT_1',
                    'EXPORT_1',
                    'EXPORT_2',
                    'EXPORT_3',
                ],
                'expectedPlaceholders' => [
                    'EXPORT_1' => VariableDependency::createExport('EXPORT_1'),
                    'EXPORT_2' => VariableDependency::createExport('EXPORT_2'),
                    'EXPORT_3' => VariableDependency::createExport('EXPORT_3'),
                ],
            ],
            'invalid type collection' => [
                'type' => 'invalid',
                'names' => [
                    'EXPORT_1',
                    'EXPORT_1',
                    'EXPORT_2',
                    'EXPORT_3',
                ],
                'expectedPlaceholders' => [
                    'EXPORT_1' => VariableDependency::createExport('EXPORT_1'),
                    'EXPORT_2' => VariableDependency::createExport('EXPORT_2'),
                    'EXPORT_3' => VariableDependency::createExport('EXPORT_3'),
                ],
            ],
        ];
    }

    public function testCreatePlaceholder()
    {
        $collection = VariableDependencyCollection::createDependencyCollection();
        $this->assertEquals([], $this->getCollectionVariablePlaceholders($collection));

        $placeholder = $collection->createPlaceholder('PLACEHOLDER');

        $this->assertInstanceOf(VariableDependency::class, $placeholder);
        $this->assertEquals(
            [
                'PLACEHOLDER' => $placeholder,
            ],
            $this->getCollectionVariablePlaceholders($collection)
        );
    }

    public function testMerge()
    {
        $collection = VariableDependencyCollection::createDependencyCollection(['ONE']);

        $collection = $collection->merge(VariableDependencyCollection::createDependencyCollection(['TWO', 'THREE']));
        $collection = $collection->merge(
            VariableDependencyCollection::createDependencyCollection(['THREE', 'FOUR'])
        );
        $collection = $collection->merge(VariableDependencyCollection::createExportCollection(['FIVE']));

        $this->assertCount(4, $collection);

        $this->assertEquals(
            [
                'ONE' => new VariableDependency('ONE', VariableDependency::TYPE_DEPENDENCY),
                'TWO' => new VariableDependency('TWO', VariableDependency::TYPE_DEPENDENCY),
                'THREE' => new VariableDependency('THREE', VariableDependency::TYPE_DEPENDENCY),
                'FOUR' => new VariableDependency('FOUR', VariableDependency::TYPE_DEPENDENCY),
            ],
            $this->getCollectionVariablePlaceholders($collection)
        );
    }

    public function testIterator()
    {
        $collectionValues = [
            'ONE' => 'ONE',
            'TWO' => 'TWO',
            'THREE' => 'THREE',
        ];

        $collection = VariableDependencyCollection::createDependencyCollection(array_values($collectionValues));

        foreach ($collection as $id => $variablePlaceholder) {
            $expectedPlaceholder = new VariableDependency(
                $collectionValues[$id],
                VariableDependency::TYPE_DEPENDENCY
            );

            $this->assertEquals($expectedPlaceholder, $variablePlaceholder);
        }
    }

    /**
     * @param VariableDependencyCollection $collection
     *
     * @return VariableDependency[]
     */
    private function getCollectionVariablePlaceholders(VariableDependencyCollection $collection): array
    {
        $reflectionObject = new \ReflectionObject($collection);
        $property = $reflectionObject->getProperty('dependencies');
        $property->setAccessible(true);

        return $property->getValue($collection);
    }
}
