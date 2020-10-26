<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocationInterface;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;

class StaticObjectMethodInvocationTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        StaticObject $staticObject,
        string $methodName,
        MethodArgumentsInterface $arguments,
        MetadataInterface $expectedMetadata
    ) {
        $invocation = new StaticObjectMethodInvocation($staticObject, $methodName, $arguments);

        $this->assertSame($staticObject, $invocation->getStaticObject());
        $this->assertSame($methodName, $invocation->getCall());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertEquals($expectedMetadata, $invocation->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments, string reference' => [
                'staticObject' => new StaticObject(
                    'parent'
                ),
                'methodName' => 'method',
                'arguments' => new MethodArguments(),
                'expectedMetadata' => new Metadata(),
            ],
            'no arguments, object reference' => [
                'staticObject' => new StaticObject(
                    ClassName::class
                ),
                'methodName' => 'method',
                'arguments' => new MethodArguments(),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ClassName::class),
                    ]),
                ]),
            ],
            'argument expressions contain additional metadata' => [
                'staticObject' => new StaticObject(
                    ClassName::class
                ),
                'methodName' => 'method',
                'arguments' => new MethodArguments([
                    new StaticObjectMethodInvocation(
                        new StaticObject(StaticObject::class),
                        'staticMethodName'
                    )
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(StaticObject::class),
                        new ClassName(ClassName::class),
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(StaticObjectMethodInvocationInterface $invocation, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $invocation);
    }

    public function renderDataProvider(): array
    {
        return [
            'object and method name only, string reference' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        'parent'
                    ),
                    'methodName'
                ),
                'expectedString' => 'parent::methodName()',
            ],
            'object and method name only, object reference' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        ClassName::class
                    ),
                    'methodName'
                ),
                'expectedString' => 'ClassName::methodName()',
            ],
            'object and method name only, object reference, class in root namespace' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        \Throwable::class
                    ),
                    'methodName'
                ),
                'expectedString' => '\Throwable::methodName()',
            ],
            'has arguments, inline' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        ClassName::class
                    ),
                    'methodName',
                    new MethodArguments([
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ])
                ),
                'expectedString' => "ClassName::methodName(1, \'single-quoted value\')",
            ],
            'has arguments, stacked' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        ClassName::class
                    ),
                    'methodName',
                    new MethodArguments(
                        [
                            new LiteralExpression('1'),
                            new LiteralExpression("\'single-quoted value\'"),
                        ],
                        MethodArguments::FORMAT_STACKED
                    )
                ),
                'expectedString' => "ClassName::methodName(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
        ];
    }
}
