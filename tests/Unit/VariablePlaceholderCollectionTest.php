<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class VariablePlaceholderCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $type
     * @param string[] $names
     * @param VariablePlaceholder[] $expectedPlaceholders
     */
    public function testCreate(string $type, array $names, array $expectedPlaceholders)
    {
        $collection = VariablePlaceholderCollection::create($type, $names);

        $this->assertCount(count($expectedPlaceholders), $collection);

        $this->assertEquals($expectedPlaceholders, $this->getCollectionVariablePlaceholders($collection));
    }

    public function createDataProvider(): array
    {
        return [
            'dependency collection' => [
                'type' => VariablePlaceholder::TYPE_DEPENDENCY,
                'names' => [
                    'DEPENDENCY_1',
                    'DEPENDENCY_2',
                    'DEPENDENCY_2',
                    'DEPENDENCY_3',
                ],
                'expectedPlaceholders' => [
                    'DEPENDENCY_1' => VariablePlaceholder::createDependency('DEPENDENCY_1'),
                    'DEPENDENCY_2' => VariablePlaceholder::createDependency('DEPENDENCY_2'),
                    'DEPENDENCY_3' => VariablePlaceholder::createDependency('DEPENDENCY_3'),
                ],
            ],
            'export collection' => [
                'type' => VariablePlaceholder::TYPE_EXPORT,
                'names' => [
                    'EXPORT_1',
                    'EXPORT_1',
                    'EXPORT_2',
                    'EXPORT_3',
                ],
                'expectedPlaceholders' => [
                    'EXPORT_1' => VariablePlaceholder::createExport('EXPORT_1'),
                    'EXPORT_2' => VariablePlaceholder::createExport('EXPORT_2'),
                    'EXPORT_3' => VariablePlaceholder::createExport('EXPORT_3'),
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
                    'EXPORT_1' => VariablePlaceholder::createExport('EXPORT_1'),
                    'EXPORT_2' => VariablePlaceholder::createExport('EXPORT_2'),
                    'EXPORT_3' => VariablePlaceholder::createExport('EXPORT_3'),
                ],
            ],
        ];
    }

    public function testCreatePlaceholder()
    {
        $collection = VariablePlaceholderCollection::createDependencyCollection();
        $this->assertEquals([], $this->getCollectionVariablePlaceholders($collection));

        $placeholder = $collection->createPlaceholder('PLACEHOLDER');

        $this->assertInstanceOf(VariablePlaceholder::class, $placeholder);
        $this->assertEquals(
            [
                'PLACEHOLDER' => $placeholder,
            ],
            $this->getCollectionVariablePlaceholders($collection)
        );
    }

    public function testMerge()
    {
        $collection = VariablePlaceholderCollection::createDependencyCollection(['ONE']);

        $collection = $collection->merge(VariablePlaceholderCollection::createDependencyCollection(['TWO', 'THREE']));
        $collection = $collection->merge(VariablePlaceholderCollection::createDependencyCollection(['THREE', 'FOUR']));
        $collection = $collection->merge(VariablePlaceholderCollection::createExportCollection(['FIVE']));

        $this->assertCount(4, $collection);

        $this->assertEquals(
            [
                'ONE' => new VariablePlaceholder('ONE', VariablePlaceholder::TYPE_DEPENDENCY),
                'TWO' => new VariablePlaceholder('TWO', VariablePlaceholder::TYPE_DEPENDENCY),
                'THREE' => new VariablePlaceholder('THREE', VariablePlaceholder::TYPE_DEPENDENCY),
                'FOUR' => new VariablePlaceholder('FOUR', VariablePlaceholder::TYPE_DEPENDENCY),
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

        $collection = VariablePlaceholderCollection::createDependencyCollection(array_values($collectionValues));

        foreach ($collection as $id => $variablePlaceholder) {
            $expectedPlaceholder = new VariablePlaceholder(
                $collectionValues[$id],
                VariablePlaceholder::TYPE_DEPENDENCY
            );

            $this->assertEquals($expectedPlaceholder, $variablePlaceholder);
        }
    }

    /**
     * @param VariablePlaceholderCollection $collection
     *
     * @return VariablePlaceholder[]
     */
    private function getCollectionVariablePlaceholders(VariablePlaceholderCollection $collection): array
    {
        $reflectionObject = new \ReflectionObject($collection);
        $property = $reflectionObject->getProperty('variablePlaceholders');
        $property->setAccessible(true);

        return $property->getValue($collection);
    }
}
