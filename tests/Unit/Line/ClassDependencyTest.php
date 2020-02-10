<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Tests\Services\ObjectReflector;

class ClassDependencyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $className, ?string $alias)
    {
        $classDependency = new ClassDependency($className, $alias);

        $this->assertSame($className, ObjectReflector::getProperty($classDependency, 'className'));
        $this->assertSame($alias, ObjectReflector::getProperty($classDependency, 'alias'));
    }

    public function createDataProvider(): array
    {
        return [
            'no alias' => [
                'className' => ClassDependencyTest::class,
                'alias' => null,
            ],
            'has alias' => [
                'className' => \PHPUnit\Framework\TestCase::class,
                'alias' => 'ClassNameAlias',
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ClassDependency $classDependency, string $expectedString)
    {
        $this->assertSame($expectedString, $classDependency->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'no alias' => [
                'comment' => new ClassDependency(ClassDependency::class),
                'expectedString' => 'use webignition\BasilCompilableSource\Line\ClassDependency;',
            ],
            'has alias' => [
                'comment' => new ClassDependency(ClassDependencyTest::class, 'BaseTest'),
                'expectedString' =>
                    'use webignition\BasilCompilableSource\Tests\Unit\Line\ClassDependencyTest as BaseTest;',
            ],
        ];
    }
}
