<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\Tests\Unit\Line\ClassDependencyTest;

class ClassDependencyCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param LineInterface[] $lines
     * @param LineInterface[] $expectedLines
     */
    public function testCreate(array $lines, array $expectedLines)
    {
        $classDependencyCollection = new ClassDependencyCollection($lines);

        $collectionLines = [];
        foreach ($classDependencyCollection->getLines() as $line) {
            $collectionLines[] = $line;
        }

        $this->assertEquals($expectedLines, $collectionLines);
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'lines' => [],
                'expectedLines' => [],
            ],
            'no class dependency lines' => [
                'lines' => [
                    new EmptyLine(),
                    new SingleLineComment(''),
                ],
                'expectedLines' => [],
            ],
            'has class dependency lines' => [
                'lines' => [
                    new EmptyLine(),
                    new SingleLineComment(''),
                    new ClassDependency(EmptyLine::class),
                    new ClassDependency(SingleLineComment::class),
                    new ClassDependency(EmptyLine::class),
                ],
                'expectedLines' => [
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
                    'use webignition\BasilCompilableSource\Line\ClassDependency;' . "\n" .
                    'use webignition\BasilCompilableSource\Tests\Unit\Line\ClassDependencyTest as BaseTest;',
            ],
            'lines are sorted' => [
                'collection' => new ClassDependencyCollection([
                    new ClassDependency('C'),
                    new ClassDependency('A'),
                    new ClassDependency('B'),
                ]),
                'expectedString' =>
                    'use A;' . "\n" .
                    'use B;' . "\n" .
                    'use C;',
            ],
        ];
    }
}
