<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\Block\DocBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassDefinition;
use webignition\BasilCompilableSource\ClassDefinitionInterface;
use webignition\BasilCompilableSource\DataProviderMethodDefinition;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodDefinition;
use webignition\BasilCompilableSource\MethodDefinitionInterface;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;

class ClassDefinitionTest extends TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $name
     * @param array<mixed> $methods
     * @param MethodDefinitionInterface[] $expectedMethods
     */
    public function testCreate(string $name, array $methods, array $expectedMethods)
    {
        $classDefinition = new ClassDefinition($name, $methods);

        $this->assertSame($name, $classDefinition->getName());
        $this->assertEquals($expectedMethods, $classDefinition->getMethods());
    }

    public function createDataProvider(): array
    {
        return [
            'no methods' => [
                'name' => 'noMethods',
                'methods' => [],
                'expectedMethods' => [],
            ],
            'invalid methods' => [
                'name' => 'noMethods',
                'methods' => [
                    1,
                    true,
                    'string',
                ],
                'expectedMethods' => [],
            ],
            'valid methods' => [
                'name' => 'noMethods',
                'methods' => [
                    new MethodDefinition('methodOne', new Body([])),
                    new MethodDefinition('methodTwo', new Body([])),
                ],
                'expectedMethods' => [
                    'methodOne' => new MethodDefinition('methodOne', new Body([])),
                    'methodTwo' => new MethodDefinition('methodTwo', new Body([])),
                ],
            ],
        ];
    }

    public function testGetBaseClass()
    {
        $classDefinition = new ClassDefinition('className', []);
        $this->assertNull($classDefinition->getBaseClass());

        $baseClass = new ClassDependency('BaseClass');

        $classDefinition->setBaseClass($baseClass);
        $this->assertEquals($baseClass, $classDefinition->getBaseClass());
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
                    'className',
                    [
                        new MethodDefinition('methodName', new Body([])),
                    ]
                ),
                'expectedMetadata' => new Metadata(),
            ],
            'methods without metadata' => [
                'classDefinition' => new ClassDefinition(
                    'className',
                    [
                        new MethodDefinition('name', new Body([
                            new EmptyLine(),
                            new SingleLineComment('single line comment'),
                        ])),
                    ]
                ),
                'expectedMetadata' => new Metadata(),
            ],
            'methods with metadata' => [
                'classDefinition' => new ClassDefinition(
                    'className',
                    [
                        new MethodDefinition('name', new Body([
                            new Statement(
                                new ObjectMethodInvocation(
                                    new VariableDependency('DEPENDENCY'),
                                    'methodName'
                                )
                            ),
                            new AssignmentStatement(
                                new VariableName('variable'),
                                new MethodInvocation('methodName')
                            ),
                        ])),
                    ]
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
        $this->assertSame($expectedString, $classDefinition->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'no methods, no base class' => [
                'classDefinition' => new ClassDefinition('NameOfClass', []),
                'expectedString' =>
                    'class NameOfClass' . "\n" .
                    '{}'
            ],
            'no methods, base class in root namespace' => [
                'classDefinition' => $this->createClassDefinitionWithBaseClass(
                    new ClassDefinition('NameOfClass', []),
                    new ClassDependency('TestCase')
                ),
                'expectedString' =>
                    'class NameOfClass extends TestCase' . "\n" .
                    '{}'
            ],
            'no methods, base class in non-root namespace' => [
                'classDefinition' => $this->createClassDefinitionWithBaseClass(
                    new ClassDefinition('NameOfClass', []),
                    new ClassDependency(TestCase::class)
                ),
                'expectedString' =>
                    'use PHPUnit\Framework\TestCase;' . "\n" .
                    "\n" .
                    'class NameOfClass extends TestCase' . "\n" .
                    '{}'
            ],
            'single empty method' => [
                'classDefinition' => $this->createClassDefinitionWithBaseClass(
                    new ClassDefinition('NameOfClass', [
                        new MethodDefinition('methodName', new Body([]))
                    ]),
                    new ClassDependency('TestCase')
                ),
                'expectedString' =>
                    'class NameOfClass extends TestCase' . "\n" .
                    '{' . "\n" .
                    '    public function methodName()' . "\n" .
                    '    {' . "\n\n" .
                    '    }' . "\n" .
                    '}'
            ],
            'many methods' => [
                'classDefinition' => $this->createClassDefinitionWithBaseClass(
                    new ClassDefinition('NameOfClass', [
                        new MethodDefinition('stepOne', new Body([
                            new SingleLineComment('click $"a"'),
                            new AssignmentStatement(
                                new VariableName('statement'),
                                new StaticObjectMethodInvocation(
                                    new StaticObject('Acme\\Statement'),
                                    'createAction',
                                    [
                                        new LiteralExpression('\'$"a" exists\''),
                                    ]
                                )
                            ),
                            new AssignmentStatement(
                                new VariableName('currentStatement'),
                                new VariableName('statement')
                            ),
                        ])),
                        new MethodDefinition('stepTwo', new Body([
                            new SingleLineComment('click $"b"'),
                            new AssignmentStatement(
                                new VariableName('statement'),
                                new StaticObjectMethodInvocation(
                                    new StaticObject('Acme\\Statement'),
                                    'createAction',
                                    [
                                        new LiteralExpression('\'$"b" exists\''),
                                    ]
                                )
                            ),
                            new AssignmentStatement(
                                new VariableName('currentStatement'),
                                new VariableName('statement')
                            ),
                        ])),
                    ]),
                    new ClassDependency('TestCase')
                ),
                'expectedString' =>
                    'use Acme\Statement;' . "\n" .
                    "\n" .
                    'class NameOfClass extends TestCase' . "\n" .
                    '{' . "\n" .
                    '    public function stepOne()' . "\n" .
                    '    {' . "\n" .
                    '        // click $"a"' . "\n" .
                    '        $statement = Statement::createAction(\'$"a" exists\');' . "\n" .
                    '        $currentStatement = $statement;' . "\n" .
                    '    }' . "\n" .
                    "\n" .
                    '    public function stepTwo()' . "\n" .
                    '    {' . "\n" .
                    '        // click $"b"' . "\n" .
                    '        $statement = Statement::createAction(\'$"b" exists\');' . "\n" .
                    '        $currentStatement = $statement;' . "\n" .
                    '    }' . "\n" .
                    '}'
            ],
            'many methods, with docblock ?????' => [
                'classDefinition' => $this->createClassDefinitionWithBaseClass(
                    new ClassDefinition('NameOfClass', [
                        $this->createMethodDefinitionWithDocBlock(
                            new MethodDefinition(
                                'stepOne',
                                new Body([
                                    new SingleLineComment('click $"a"'),
                                    new AssignmentStatement(
                                        new VariableName('statement'),
                                        new StaticObjectMethodInvocation(
                                            new StaticObject('Acme\\Statement'),
                                            'createAction',
                                            [
                                                new LiteralExpression('\'$"a" exists\''),
                                            ]
                                        )
                                    ),
                                    new AssignmentStatement(
                                        new VariableName('currentStatement'),
                                        new VariableName('statement')
                                    ),
                                ]),
                                [
                                    'x', 'y',
                                ]
                            ),
                            new DocBlock([
                                '@dataProvider stepOneDataProvider',
                            ])
                        ),
                        new DataProviderMethodDefinition('stepOneDataProvider', [
                            0 => [
                                'x' => '1',
                                'y' => '2',
                            ],
                            1 => [
                                'x' => '3',
                                'y' => '4',
                            ],
                        ]),
                        new MethodDefinition('stepTwo', new Body([
                            new SingleLineComment('click $"b"'),
                            new AssignmentStatement(
                                new VariableName('statement'),
                                new StaticObjectMethodInvocation(
                                    new StaticObject('Acme\\Statement'),
                                    'createAction',
                                    [
                                        new LiteralExpression('\'$"b" exists\''),
                                    ]
                                )
                            ),
                            new AssignmentStatement(
                                new VariableName('currentStatement'),
                                new VariableName('statement')
                            ),
                        ])),
                    ]),
                    new ClassDependency('TestCase')
                ),
                'expectedString' =>
                    'use Acme\Statement;' . "\n" .
                    "\n" .
                    'class NameOfClass extends TestCase' . "\n" .
                    '{' . "\n" .
                    '    /**' . "\n" .
                    '     * @dataProvider stepOneDataProvider' . "\n" .
                    '     */' . "\n" .
                    '    public function stepOne($x, $y)' . "\n" .
                    '    {' . "\n" .
                    '        // click $"a"' . "\n" .
                    '        $statement = Statement::createAction(\'$"a" exists\');' . "\n" .
                    '        $currentStatement = $statement;' . "\n" .
                    '    }' . "\n" .
                    "\n" .
                    '    public function stepOneDataProvider(): array' . "\n" .
                    '    {' . "\n" .
                    '        return [' . "\n" .
                    '            \'0\' => [' . "\n" .
                    '                \'x\' => \'1\',' . "\n" .
                    '                \'y\' => \'2\',' . "\n" .
                    '            ],' . "\n" .
                    '            \'1\' => [' . "\n" .
                    '                \'x\' => \'3\',' . "\n" .
                    '                \'y\' => \'4\',' . "\n" .
                    '            ],' . "\n" .
                    '        ];' . "\n" .
                    '    }' . "\n" .
                    "\n" .
                    '    public function stepTwo()' . "\n" .
                    '    {' . "\n" .
                    '        // click $"b"' . "\n" .
                    '        $statement = Statement::createAction(\'$"b" exists\');' . "\n" .
                    '        $currentStatement = $statement;' . "\n" .
                    '    }' . "\n" .
                    '}'
            ],
        ];
    }

    private function createMethodDefinitionWithDocBlock(
        MethodDefinition $methodDefinition,
        DocBlock $docBlock
    ): MethodDefinitionInterface {
        $methodDefinition->setDocBlock($docBlock);

        return $methodDefinition;
    }

    private function createClassDefinitionWithBaseClass(
        ClassDefinition $classDefinition,
        ClassDependency $baseClass
    ): ClassDefinitionInterface {
        $classDefinition->setBaseClass($baseClass);

        return $classDefinition;
    }
}
