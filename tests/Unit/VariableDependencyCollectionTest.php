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
     * @param string[] $names
     * @param VariableDependency[] $expectedPlaceholders
     */
    public function testCreate(array $names, array $expectedPlaceholders)
    {
        $collection = new VariableDependencyCollection($names);

        $this->assertCount(count($expectedPlaceholders), $collection);
        $this->assertEquals($expectedPlaceholders, $this->getCollectionVariablePlaceholders($collection));
    }

    public function createDataProvider(): array
    {
        return [
            'default' => [
                'names' => [
                    'DEPENDENCY_1',
                    'DEPENDENCY_2',
                    'DEPENDENCY_2',
                    'DEPENDENCY_3',
                ],
                'expectedPlaceholders' => [
                    'DEPENDENCY_1' => new VariableDependency('DEPENDENCY_1'),
                    'DEPENDENCY_2' => new VariableDependency('DEPENDENCY_2'),
                    'DEPENDENCY_3' => new VariableDependency('DEPENDENCY_3'),
                ],
            ],
        ];
    }

    public function testMerge()
    {
        $collection = new VariableDependencyCollection(['ONE']);

        $collection = $collection->merge(new VariableDependencyCollection(['TWO', 'THREE']));
        $collection = $collection->merge(
            new VariableDependencyCollection(['THREE', 'FOUR'])
        );

        $this->assertCount(4, $collection);

        $this->assertEquals(
            [
                'ONE' => new VariableDependency('ONE'),
                'TWO' => new VariableDependency('TWO'),
                'THREE' => new VariableDependency('THREE'),
                'FOUR' => new VariableDependency('FOUR'),
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

        $collection = new VariableDependencyCollection(array_values($collectionValues));

        foreach ($collection as $id => $variablePlaceholder) {
            $expectedPlaceholder = new VariableDependency($collectionValues[$id]);

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
