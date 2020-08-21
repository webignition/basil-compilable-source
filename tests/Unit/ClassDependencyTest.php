<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\ClassDependency;
use webignition\ObjectReflector\ObjectReflector;

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
                'classDependency' => new ClassDependency(ClassDependency::class),
                'expectedString' => 'use webignition\BasilCompilableSource\ClassDependency;',
            ],
            'has alias' => [
                'classDependency' => new ClassDependency(ClassDependencyTest::class, 'BaseTest'),
                'expectedString' =>
                    'use webignition\BasilCompilableSource\Tests\Unit\ClassDependencyTest as BaseTest;',
            ],
        ];
    }

    /**
     * @dataProvider isInRootNamespaceDataProvider
     */
    public function testIsInRootNamespace(ClassDependency $classDependency, bool $expectedIsInRootNamespace)
    {
        $this->assertSame($expectedIsInRootNamespace, $classDependency->isInRootNamespace());
    }

    public function isInRootNamespaceDataProvider(): array
    {
        return [
            'not in root namespace, no alias' => [
                'classDependency' => new ClassDependency(ClassDependency::class),
                'expectedIsInRootNamespace' => false,
            ],
            'not in root namespace, has alias' => [
                'classDependency' => new ClassDependency(ClassDependencyTest::class, 'BaseTest'),
                'expectedIsInRootNamespace' => false,
            ],
            'is in root namespace, no alias' => [
                'classDependency' => new ClassDependency(\Throwable::class),
                'expectedIsInRootNamespace' => true,
            ],
            'is in root namespace, has alias' => [
                'classDependency' => new ClassDependency(\Throwable::class, 'Bouncy'),
                'expectedIsInRootNamespace' => true,
            ],
        ];
    }
}
