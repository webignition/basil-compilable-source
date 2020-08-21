<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\ClassName;
use webignition\ObjectReflector\ObjectReflector;

class ClassNameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $className, ?string $alias)
    {
        $classDependency = new ClassName($className, $alias);

        $this->assertSame($className, $classDependency->getClassName());
        $this->assertSame($alias, ObjectReflector::getProperty($classDependency, 'alias'));
    }

    public function createDataProvider(): array
    {
        return [
            'no alias' => [
                'className' => ClassNameTest::class,
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
    public function testGetClass(ClassName $classDependency, string $expectedClass)
    {
        $this->assertSame($expectedClass, $classDependency->getClass());
    }

    public function getClassDataProvider(): array
    {
        return [
            'global namespace' => [
                'className' => new ClassName('Global'),
                'expectedClass' => 'Global',
            ],
            'namespaced' => [
                'className' => new ClassName(ClassName::class),
                'expectedClass' => 'ClassName',
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ClassName $classDependency, string $expectedString)
    {
        $this->assertSame($expectedString, $classDependency->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'no alias' => [
                'className' => new ClassName(ClassName::class),
                'expectedString' => 'ClassName',
            ],
            'has alias' => [
                'className' => new ClassName(ClassNameTest::class, 'BaseTest'),
                'expectedString' => 'BaseTest',
            ],
            'no alias, in root namespace' => [
                'className' => new ClassName(\Throwable::class),
                'expectedString' => 'Throwable',
            ],
            'has alias, in root namespace' => [
                'className' => new ClassName(\Throwable::class, 'Bouncy'),
                'expectedString' => 'Bouncy',
            ],
        ];
    }

    /**
     * @dataProvider isInRootNamespaceDataProvider
     */
    public function testIsInRootNamespace(ClassName $classDependency, bool $expectedIsInRootNamespace)
    {
        $this->assertSame($expectedIsInRootNamespace, $classDependency->isInRootNamespace());
    }

    public function isInRootNamespaceDataProvider(): array
    {
        return [
            'not in root namespace, no alias' => [
                'className' => new ClassName(ClassName::class),
                'expectedIsInRootNamespace' => false,
            ],
            'not in root namespace, has alias' => [
                'className' => new ClassName(ClassNameTest::class, 'BaseTest'),
                'expectedIsInRootNamespace' => false,
            ],
            'is in root namespace, no alias' => [
                'className' => new ClassName(\Throwable::class),
                'expectedIsInRootNamespace' => true,
            ],
            'is in root namespace, has alias' => [
                'className' => new ClassName(\Throwable::class, 'Bouncy'),
                'expectedIsInRootNamespace' => true,
            ],
        ];
    }

    /**
     * @dataProvider renderClassNameDataProvider
     */
    public function testRenderClassName(ClassName $classDependency, string $expectedString)
    {
        $this->assertSame($expectedString, $classDependency->renderClassName());
    }

    public function renderClassNameDataProvider(): array
    {
        return [
            'no alias' => [
                'className' => new ClassName(ClassName::class),
                'expectedString' => 'ClassName',
            ],
            'has alias' => [
                'className' => new ClassName(ClassNameTest::class, 'BaseTest'),
                'expectedString' => 'BaseTest',
            ],
            'no alias, in root namespace' => [
                'className' => new ClassName(\Throwable::class),
                'expectedString' => '\Throwable',
            ],
            'has alias, in root namespace' => [
                'className' => new ClassName(\Throwable::class, 'Bouncy'),
                'expectedString' => 'Bouncy',
            ],
        ];
    }

    /**
     * @dataProvider isFullyQualifiedClassNameDataProvider
     */
    public function testIsFullyQualifiedClassName(string $className, bool $expectedIsFullyQualifiedClassName)
    {
        self::assertSame($expectedIsFullyQualifiedClassName, ClassName::isFullyQualifiedClassName($className));
    }

    public function isFullyQualifiedClassNameDataProvider(): array
    {
        return [
            'namespaced class name' => [
                'className' => TestCase::class,
                'expectedIsFullyQualifiedClassName' => true,
            ],
            'root-namespaced class name' => [
                'className' => \Throwable::class,
                'expectedIsFullyQualifiedClassName' => true,
            ],
            'self' => [
                'className' => 'self',
                'expectedIsFullyQualifiedClassName' => false,
            ],
            'static' => [
                'className' => 'static',
                'expectedIsFullyQualifiedClassName' => false,
            ],
            'parent' => [
                'className' => 'parent',
                'expectedIsFullyQualifiedClassName' => false,
            ],
        ];
    }
}
