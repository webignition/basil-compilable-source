<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\EmptyLine;
use webignition\BasilCompilableSource\SingleLineComment;
use webignition\BasilCompilableSource\Tests\Unit\ClassNameTest;
use webignition\ObjectReflector\ObjectReflector;

class ClassDependencyCollectionTest extends \PHPUnit\Framework\TestCase
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
                    new ClassName(EmptyLine::class),
                    new ClassName(SingleLineComment::class),
                    new ClassName(EmptyLine::class),
                ],
                'expectedDependencies' => [
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
            'single item in root namespace' => [
                'collection' => new ClassDependencyCollection([
                    new ClassName(\Throwable::class),
                ]),
                'expectedString' => '',
            ],
            'items in root namespace and not in root namespace' => [
                'collection' => new ClassDependencyCollection([
                    new ClassName('Acme\A'),
                    new ClassName('B'),
                    new ClassName('Acme\C'),
                ]),
                'expectedString' =>
                    'use Acme\A;' . "\n" .
                    'use Acme\C;',
            ],
        ];
    }
}
