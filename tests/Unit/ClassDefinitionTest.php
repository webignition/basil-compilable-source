<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassBody;
use webignition\BasilCompilableSource\ClassDefinition;
use webignition\BasilCompilableSource\ClassDefinitionInterface;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\ClassSignature;
use webignition\BasilCompilableSource\EmptyLine;
use webignition\BasilCompilableSource\Expression\AssignmentExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodDefinition;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\SingleLineComment;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;

class ClassDefinitionTest extends AbstractResolvableTest
{
    public function testCreate()
    {
        $signature = new ClassSignature('ClassName');
        $body = new ClassBody([]);

        $classDefinition = new ClassDefinition($signature, $body);

        self::assertSame($signature, $classDefinition->getSignature());
        self::assertSame($body, $classDefinition->getBody());
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ClassDefinitionInterface $classDefinition, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $classDefinition->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'classDefinition' => new ClassDefinition(
                    new ClassSignature('className'),
                    new ClassBody([])
                ),
                'expectedMetadata' => new Metadata(),
            ],
            'methods without metadata' => [
                'classDefinition' => new ClassDefinition(
                    new ClassSignature('className'),
                    new ClassBody([
                        new MethodDefinition('name', new Body([
                            new EmptyLine(),
                            new SingleLineComment('single line comment'),
                        ])),
                    ])
                ),
                'expectedMetadata' => new Metadata(),
            ],
            'methods with metadata' => [
                'classDefinition' => new ClassDefinition(
                    new ClassSignature('className'),
                    new ClassBody([
                        new MethodDefinition('name', new Body([
                            new Statement(
                                new ObjectMethodInvocation(
                                    new VariableDependency('DEPENDENCY'),
                                    'methodName'
                                )
                            ),
                            new Statement(
                                new AssignmentExpression(
                                    new VariableName('variable'),
                                    new MethodInvocation('methodName')
                                )
                            )
                        ])),
                    ])
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'DEPENDENCY',
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ClassDefinitionInterface $classDefinition, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $classDefinition);
    }

    public function renderDataProvider(): array
    {
        return [
            'no methods, no base class' => [
                'classDefinition' => new ClassDefinition(
                    new ClassSignature('NameOfClass'),
                    new ClassBody([])
                ),
                'expectedString' =>
                    'class NameOfClass' . "\n" .
                    '{}'
            ],
            'no methods, base class in root namespace' => [
                'classDefinition' => new ClassDefinition(
                    new ClassSignature(
                        'NameOfClass',
                        new ClassName('TestCase')
                    ),
                    new ClassBody([])
                ),
                'expectedString' =>
                    'class NameOfClass extends \TestCase' . "\n" .
                    '{}'
            ],
            'no methods, base class in non-root namespace' => [
                'classDefinition' => new ClassDefinition(
                    new ClassSignature(
                        'NameOfClass',
                        new ClassName(TestCase::class)
                    ),
                    new ClassBody([])
                ),
                'expectedString' =>
                    'use PHPUnit\Framework\TestCase;' . "\n" .
                    "\n" .
                    'class NameOfClass extends TestCase' . "\n" .
                    '{}'
            ],
            'has method' => [
                'classDefinition' => new ClassDefinition(
                    new ClassSignature(
                        'NameOfClass',
                        new ClassName('TestCase')
                    ),
                    new ClassBody([
                        new MethodDefinition('methodName', new Body([])),
                    ])
                ),
                'expectedString' =>
                    'class NameOfClass extends \TestCase' . "\n" .
                    '{' . "\n" .
                    '    public function methodName()' . "\n" .
                    '    {' . "\n\n" .
                    '    }' . "\n" .
                    '}'
            ],
        ];
    }
}
