<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassDependency;
use webignition\BasilCompilableSource\EmptyLine;
use webignition\BasilCompilableSource\SingleLineComment;
use webignition\BasilCompilableSource\Tests\Unit\ClassDependencyTest;
use webignition\ObjectReflector\ObjectReflector;

class ClassDependencyCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param ClassDependency[] $dependencies
     * @param ClassDependency[] $expectedDependencies
     */
    public function testCreate(array $dependencies, array $expectedDependencies)
    {
        $collection = new ClassDependencyCollection($dependencies);

        $this->assertEquals($expectedDependencies, ObjectReflector::getProperty($collection, 'dependencies'));
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'dependencies' => [],
                'expectedDependencies' => [],
            ],
            'no class dependency lines' => [
                'dependencies' => [
                    new EmptyLine(),
                    new SingleLineComment(''),
                ],
                'expectedDependencies' => [],
            ],
            'has class dependency lines' => [
                'dependencies' => [
                    new EmptyLine(),
                    new SingleLineComment(''),
                    new ClassDependency(EmptyLine::class),
                    new ClassDependency(SingleLineComment::class),
                    new ClassDependency(EmptyLine::class),
                ],
                'expectedDependencies' => [
                    new ClassDependency(EmptyLine::class),
                    new ClassDependency(SingleLineComment::class),
                ],
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ClassDependencyCollection $collection, string $expectedString)
    {
        $this->assertSame($expectedString, $collection->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'collection' => new ClassDependencyCollection([]),
                'expectedString' => '',
            ],
            'non-empty' => [
                'collection' => new ClassDependencyCollection([
                    new ClassDependency(ClassDependency::class),
                    new ClassDependency(ClassDependencyTest::class, 'BaseTest'),
                ]),
                'expectedString' =>
                    'use webignition\BasilCompilableSource\ClassDependency;' . "\n" .
                    'use webignition\BasilCompilableSource\Tests\Unit\ClassDependencyTest as BaseTest;',
            ],
            'lines are sorted' => [
                'collection' => new ClassDependencyCollection([
                    new ClassDependency('Acme\C'),
                    new ClassDependency('Acme\A'),
                    new ClassDependency('Acme\B'),
                ]),
                'expectedString' =>
                    'use Acme\A;' . "\n" .
                    'use Acme\B;' . "\n" .
                    'use Acme\C;',
            ],
            'single item in root namespace' => [
                'collection' => new ClassDependencyCollection([
                    new ClassDependency(\Throwable::class),
                ]),
                'expectedString' => '',
            ],
            'items in root namespace and not in root namespace' => [
                'collection' => new ClassDependencyCollection([
                    new ClassDependency('Acme\A'),
                    new ClassDependency('B'),
                    new ClassDependency('Acme\C'),
                ]),
                'expectedString' =>
                    'use Acme\A;' . "\n" .
                    'use Acme\C;',
            ],
        ];
    }
}
