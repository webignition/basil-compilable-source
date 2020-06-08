<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSource\Tests\Services\ObjectReflector;

class ClassDependencyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $className, ?string $alias)
    {
        $classDependency = new ClassDependency($className, $alias);

        $this->assertSame($className, $classDependency->getClassName());
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
     * @dataProvider getClassDataProvider
     */
    public function testGetClass(ClassDependency $classDependency, string $expectedClass)
    {
        $this->assertSame($expectedClass, $classDependency->getClass());
    }

    public function getClassDataProvider(): array
    {
        return [
            'global namespace' => [
                'classDependency' => new ClassDependency('Global'),
                'expectedClass' => 'Global',
            ],
            'namespaced' => [
                'classDependency' => new ClassDependency(ClassDependency::class),
                'expectedClass' => 'ClassDependency',
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
                'expectedString' => 'use webignition\BasilCompilableSource\Expression\ClassDependency;',
            ],
            'has alias' => [
                'comment' => new ClassDependency(ClassDependencyTest::class, 'BaseTest'),
                'expectedString' =>
                    'use webignition\BasilCompilableSource\Tests\Unit\Expression\ClassDependencyTest as BaseTest;',
            ],
        ];
    }
}
