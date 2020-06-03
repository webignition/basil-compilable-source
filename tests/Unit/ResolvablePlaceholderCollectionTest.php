<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\ResolvablePlaceholder;
use webignition\BasilCompilableSource\ResolvablePlaceholderCollection;

class ResolvablePlaceholderCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $type
     * @param string[] $names
     * @param ResolvablePlaceholder[] $expectedPlaceholders
     */
    public function testCreate(string $type, array $names, array $expectedPlaceholders)
    {
        $collection = ResolvablePlaceholderCollection::create($type, $names);

        $this->assertCount(count($expectedPlaceholders), $collection);

        $this->assertEquals($expectedPlaceholders, $this->getCollectionVariablePlaceholders($collection));
    }

    public function createDataProvider(): array
    {
        return [
            'dependency collection' => [
                'type' => ResolvablePlaceholder::TYPE_DEPENDENCY,
                'names' => [
                    'DEPENDENCY_1',
                    'DEPENDENCY_2',
                    'DEPENDENCY_2',
                    'DEPENDENCY_3',
                ],
                'expectedPlaceholders' => [
                    'DEPENDENCY_1' => ResolvablePlaceholder::createDependency('DEPENDENCY_1'),
                    'DEPENDENCY_2' => ResolvablePlaceholder::createDependency('DEPENDENCY_2'),
                    'DEPENDENCY_3' => ResolvablePlaceholder::createDependency('DEPENDENCY_3'),
                ],
            ],
            'export collection' => [
                'type' => ResolvablePlaceholder::TYPE_EXPORT,
                'names' => [
                    'EXPORT_1',
                    'EXPORT_1',
                    'EXPORT_2',
                    'EXPORT_3',
                ],
                'expectedPlaceholders' => [
                    'EXPORT_1' => ResolvablePlaceholder::createExport('EXPORT_1'),
                    'EXPORT_2' => ResolvablePlaceholder::createExport('EXPORT_2'),
                    'EXPORT_3' => ResolvablePlaceholder::createExport('EXPORT_3'),
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
                    'EXPORT_1' => ResolvablePlaceholder::createExport('EXPORT_1'),
                    'EXPORT_2' => ResolvablePlaceholder::createExport('EXPORT_2'),
                    'EXPORT_3' => ResolvablePlaceholder::createExport('EXPORT_3'),
                ],
            ],
        ];
    }

    public function testCreatePlaceholder()
    {
        $collection = ResolvablePlaceholderCollection::createDependencyCollection();
        $this->assertEquals([], $this->getCollectionVariablePlaceholders($collection));

        $placeholder = $collection->createPlaceholder('PLACEHOLDER');

        $this->assertInstanceOf(ResolvablePlaceholder::class, $placeholder);
        $this->assertEquals(
            [
                'PLACEHOLDER' => $placeholder,
            ],
            $this->getCollectionVariablePlaceholders($collection)
        );
    }

    public function testMerge()
    {
        $collection = ResolvablePlaceholderCollection::createDependencyCollection(['ONE']);

        $collection = $collection->merge(ResolvablePlaceholderCollection::createDependencyCollection(['TWO', 'THREE']));
        $collection = $collection->merge(
            ResolvablePlaceholderCollection::createDependencyCollection(['THREE', 'FOUR'])
        );
        $collection = $collection->merge(ResolvablePlaceholderCollection::createExportCollection(['FIVE']));

        $this->assertCount(4, $collection);

        $this->assertEquals(
            [
                'ONE' => new ResolvablePlaceholder('ONE', ResolvablePlaceholder::TYPE_DEPENDENCY),
                'TWO' => new ResolvablePlaceholder('TWO', ResolvablePlaceholder::TYPE_DEPENDENCY),
                'THREE' => new ResolvablePlaceholder('THREE', ResolvablePlaceholder::TYPE_DEPENDENCY),
                'FOUR' => new ResolvablePlaceholder('FOUR', ResolvablePlaceholder::TYPE_DEPENDENCY),
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

        $collection = ResolvablePlaceholderCollection::createDependencyCollection(array_values($collectionValues));

        foreach ($collection as $id => $variablePlaceholder) {
            $expectedPlaceholder = new ResolvablePlaceholder(
                $collectionValues[$id],
                ResolvablePlaceholder::TYPE_DEPENDENCY
            );

            $this->assertEquals($expectedPlaceholder, $variablePlaceholder);
        }
    }

    /**
     * @param ResolvablePlaceholderCollection $collection
     *
     * @return ResolvablePlaceholder[]
     */
    private function getCollectionVariablePlaceholders(ResolvablePlaceholderCollection $collection): array
    {
        $reflectionObject = new \ReflectionObject($collection);
        $property = $reflectionObject->getProperty('variablePlaceholders');
        $property->setAccessible(true);

        return $property->getValue($collection);
    }
}
