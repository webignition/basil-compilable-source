<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\EmptyLine;
use webignition\BasilCompilableSource\SingleLineComment;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\Tests\Unit\ClassNameTest;
use webignition\ObjectReflector\ObjectReflector;

class ClassDependencyCollectionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param ClassName[] $dependencies
     * @param ClassName[] $expectedDependencies
     */
    public function testCreate(array $dependencies, array $expectedDependencies)
    {
        $collection = new ClassDependencyCollection($dependencies);

        $this->assertEquals($expectedDependencies, ObjectReflector::getProperty($collection, 'classNames'));
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'classNames' => [],
                'expectedClassNames' => [],
            ],
            'no class dependency lines' => [
                'classNames' => [
                    new EmptyLine(),
                    new SingleLineComment(''),
                ],
                'expectedClassNames' => [],
            ],
            'has class dependency lines' => [
                'classNames' => [
                    new EmptyLine(),
                    new SingleLineComment(''),
                    new ClassName(EmptyLine::class),
                    new ClassName(SingleLineComment::class),
                    new ClassName(EmptyLine::class),
                ],
                'expectedClassNames' => [
                    new ClassName(EmptyLine::class),
                    new ClassName(SingleLineComment::class),
                ],
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ClassDependencyCollection $collection, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $collection);
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
                    new ClassName(ClassName::class),
                    new ClassName(ClassNameTest::class, 'BaseTest'),
                ]),
                'expectedString' =>
                    'use webignition\BasilCompilableSource\ClassName;' . "\n" .
                    'use webignition\BasilCompilableSource\Tests\Unit\ClassNameTest as BaseTest;',
            ],
            'lines are sorted' => [
                'collection' => new ClassDependencyCollection([
                    new ClassName('Acme\C'),
                    new ClassName('Acme\A'),
                    new ClassName('Acme\B'),
                ]),
                'expectedString' =>
                    'use Acme\A;' . "\n" .
                    'use Acme\B;' . "\n" .
                    'use Acme\C;',
            ],
        ];
    }

    /**
     * @dataProvider countDataProvider
     */
    public function testCount(ClassDependencyCollection $collection, int $expectedCount)
    {
        self::assertSame($expectedCount, count($collection));
    }

    /**
     * @dataProvider countDataProvider
     */
    public function testCountable(ClassDependencyCollection $collection, int $expectedCount)
    {
        self::assertCount($expectedCount, $collection);
    }

    public function countDataProvider(): array
    {
        return [
            'empty' => [
                'collection' => new ClassDependencyCollection(),
                'expectedCount' => 0,
            ],
            'one' => [
                'collection' => new ClassDependencyCollection([
                    new ClassName('Acme\A'),
                ]),
                'expectedCount' => 1,
            ],
            'two' => [
                'collection' => new ClassDependencyCollection([
                    new ClassName('Acme\A'),
                    new ClassName('Acme\B'),
                ]),
                'expectedCount' => 2,
            ],
            'three' => [
                'collection' => new ClassDependencyCollection([
                    new ClassName('Acme\A'),
                    new ClassName('Acme\B'),
                    new ClassName('Acme\C'),
                ]),
                'expectedCount' => 3,
            ],
        ];
    }

    /**
     * @dataProvider isEmptyDataProvider
     */
    public function testIsEmpty(ClassDependencyCollection $collection, bool $expectedIsEmpty)
    {
        self::assertSame($expectedIsEmpty, $collection->isEmpty());
    }

    public function isEmptyDataProvider(): array
    {
        return [
            'empty' => [
                'collection' => new ClassDependencyCollection(),
                'expectedIsEmpty' => true,
            ],
            'not empty' => [
                'collection' => new ClassDependencyCollection([
                    new ClassName('Acme\A'),
                ]),
                'expectedIsEmpty' => false,
            ],
        ];
    }
}
