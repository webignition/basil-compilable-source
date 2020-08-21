<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocationInterface;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectMethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param StaticObject $staticObject
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     */
    public function testCreate(
        StaticObject $staticObject,
        string $methodName,
        array $arguments,
        string $argumentFormat,
        MetadataInterface $expectedMetadata
    ) {
        $invocation = new StaticObjectMethodInvocation($staticObject, $methodName, $arguments, $argumentFormat);

        $this->assertSame($staticObject, $invocation->getStaticObject());
        $this->assertSame($methodName, $invocation->getMethodName());
        $this->assertSame($arguments, $invocation->getArguments());
        $this->assertSame($argumentFormat, $invocation->getArgumentFormat());
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
                'arguments' => [],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
                'expectedMetadata' => new Metadata(),
            ],
            'no arguments, object reference' => [
                'staticObject' => new StaticObject(
                    ClassName::class
                ),
                'methodName' => 'method',
                'arguments' => [],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ClassName::class),
                    ]),
                ]),
            ],
            'single argument' => [
                'staticObject' => new StaticObject(
                    ClassName::class
                ),
                'methodName' => 'method',
                'arguments' => [
                    new LiteralExpression('1'),
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ClassName::class),
                    ]),
                ]),
            ],
            'multiple arguments, inline' => [
                'staticObject' => new StaticObject(
                    ClassName::class
                ),
                'methodName' => 'method',
                'arguments' => [
                    new LiteralExpression('2'),
                    new LiteralExpression("'single-quoted value'"),
                    new LiteralExpression('"double-quoted value"'),
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_INLINE,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ClassName::class),
                    ]),
                ]),
            ],
            'multiple arguments, stacked' => [
                'staticObject' => new StaticObject(
                    ClassName::class
                ),
                'methodName' => 'method',
                'arguments' => [
                    new LiteralExpression('2'),
                    new LiteralExpression("'single-quoted value'"),
                    new LiteralExpression('"double-quoted value"'),
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_STACKED,
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
                'arguments' => [
                    new StaticObjectMethodInvocation(
                        new StaticObject(StaticObject::class),
                        'staticMethodName'
                    )
                ],
                'argumentFormat' => MethodInvocation::ARGUMENT_FORMAT_STACKED,
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
        $this->assertSame($expectedString, $invocation->render());
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
            'no arguments, inline' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        ClassName::class
                    ),
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => 'ClassName::methodName()',
            ],
            'no arguments, stacked' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        ClassName::class
                    ),
                    'methodName',
                    [],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => 'ClassName::methodName()',
            ],
            'has arguments, inline' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        ClassName::class
                    ),
                    'methodName',
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                ),
                'expectedString' => "ClassName::methodName(1, \'single-quoted value\')",
            ],
            'has arguments, stacked' => [
                'invocation' => new StaticObjectMethodInvocation(
                    new StaticObject(
                        ClassName::class
                    ),
                    'methodName',
                    [
                        new LiteralExpression('1'),
                        new LiteralExpression("\'single-quoted value\'"),
                    ],
                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                ),
                'expectedString' => "ClassName::methodName(\n" .
                    "    1,\n" .
                    "    \'single-quoted value\'\n" .
                    ")",
            ],
            'object and method name only, string reference, has errors suppressed' => [
                'invocation' => $this->createInvocationWithErrorSuppression(
                    new StaticObject(
                        'parent'
                    ),
                    'methodName'
                ),
                'expectedString' => '@parent::methodName()',
            ],
        ];
    }

    private function createInvocationWithErrorSuppression(
        StaticObject $staticObject,
        string $name
    ): StaticObjectMethodInvocationInterface {
        $methodInvocation = new StaticObjectMethodInvocation($staticObject, $name);
        $methodInvocation->enableErrorSuppression();

        return $methodInvocation;
    }
}
