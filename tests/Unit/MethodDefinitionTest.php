<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Block\DocBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodDefinition;
use webignition\BasilCompilableSource\MethodDefinitionInterface;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;

class MethodDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $name
     * @param BodyInterface $codeBlock
     * @param string[] $arguments
     */
    public function testCreate(string $name, BodyInterface $codeBlock, array $arguments = [])
    {
        $methodDefinition = new MethodDefinition($name, $codeBlock, $arguments);

        $this->assertSame($name, $methodDefinition->getName());
        $this->assertEquals($codeBlock->getMetadata(), $methodDefinition->getMetadata());
        $this->assertSame($arguments, $methodDefinition->getArguments());
        $this->assertsame(MethodDefinition::VISIBILITY_PUBLIC, $methodDefinition->getVisibility());
        $this->assertNull($methodDefinition->getReturnType());
        $this->assertFalse($methodDefinition->isStatic());
        $this->assertNull($methodDefinition->getDocBlock());
    }

    public function createDataProvider(): array
    {
        $body = new Body([]);

        return [
            'no arguments' => [
                'name' => 'noArguments',
                'body' => $body,
            ],
            'empty arguments' => [
                'name' => 'emptyArguments',
                'body' => $body,
                'arguments' => [],
            ],
            'has arguments' => [
                'name' => 'hasArguments',
                'body' => $body,
                'arguments' => [
                    'arg1',
                    'arg2',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(MethodDefinitionInterface $methodDefinition, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $methodDefinition->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'methodDefinition' => new MethodDefinition('name', new Body([])),
                'expectedMetadata' => new Metadata(),
            ],
            'lines without metadata' => [
                'methodDefinition' => new MethodDefinition('name', new Body([
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
                ])),
                'expectedMetadata' => new Metadata(),
            ],
            'lines with metadata' => [
                'methodDefinition' => new MethodDefinition('name', new Body([
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
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'DEPENDENCY',
                    ]),
                ]),
            ],
        ];
    }

    public function testVisibility()
    {
        $methodDefinition = new MethodDefinition('name', new Body([]));
        $this->assertSame(MethodDefinition::VISIBILITY_PUBLIC, $methodDefinition->getVisibility());

        $methodDefinition->setProtected();
        $this->assertSame(MethodDefinition::VISIBILITY_PROTECTED, $methodDefinition->getVisibility());

        $methodDefinition->setPrivate();
        $this->assertSame(MethodDefinition::VISIBILITY_PRIVATE, $methodDefinition->getVisibility());

        $methodDefinition->setPublic();
        $this->assertSame(MethodDefinition::VISIBILITY_PUBLIC, $methodDefinition->getVisibility());
    }

    public function testSetReturnType()
    {
        $methodDefinition = new MethodDefinition('name', new Body([]));
        $this->assertNull($methodDefinition->getReturnType());

        $methodDefinition->setReturnType('string');
        $this->assertSame('string', $methodDefinition->getReturnType());

        $methodDefinition->setReturnType('void');
        $this->assertSame('void', $methodDefinition->getReturnType());

        $methodDefinition->setReturnType(null);
        $this->assertNull($methodDefinition->getReturnType());
    }

    public function testIsStatic()
    {
        $methodDefinition = new MethodDefinition('name', new Body([]));
        $this->assertFalse($methodDefinition->isStatic());

        $methodDefinition->setStatic();
        $this->assertTrue($methodDefinition->isStatic());
    }

    public function testSetDocBlock()
    {
        $methodDefinition = new MethodDefinition('name', new Body([]));
        $this->assertNull($methodDefinition->getDocBlock());

        $docBlock = new DocBlock([]);
        $methodDefinition->setDocBlock($docBlock);
        $this->assertSame($docBlock, $methodDefinition->getDocBlock());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(MethodDefinitionInterface $methodDefinition, string $expectedString)
    {
        $this->assertSame($expectedString, $methodDefinition->render());
    }

    public function renderDataProvider(): array
    {
        $emptyProtectedMethod = new MethodDefinition('emptyProtectedMethod', new Body([]));
        $emptyProtectedMethod->setProtected();

        $emptyPrivateMethod = new MethodDefinition('emptyPrivateMethod', new Body([]));
        $emptyPrivateMethod->setPrivate();

        $emptyMethodWithReturnType = new MethodDefinition('emptyPublicMethodWithReturnType', new Body([]));
        $emptyMethodWithReturnType->setReturnType('string');

        $emptyPublicStaticMethod = new MethodDefinition('emptyPublicStaticMethod', new Body([]));
        $emptyPublicStaticMethod->setStatic();

        return [
            'public, no arguments, no return type, no lines' => [
                'methodDefinition' => new MethodDefinition('emptyPublicMethod', new Body([])),
                'expectedString' =>
                    'public function emptyPublicMethod()' . "\n" .
                    '{' . "\n\n" .
                    '}'
            ],
            'protected, no arguments, no return type, no lines' => [
                'methodDefinition' => $emptyProtectedMethod,
                'expectedString' =>
                    'protected function emptyProtectedMethod()' . "\n" .
                    '{' . "\n\n" .
                    '}'
            ],
            'private, no arguments, no return type, no lines' => [
                'methodDefinition' => $emptyPrivateMethod,
                'expectedString' =>
                    'private function emptyPrivateMethod()' . "\n" .
                    '{' . "\n\n" .
                    '}'
            ],
            'public, has arguments, no return type, no lines' => [
                'methodDefinition' => new MethodDefinition('emptyPublicMethod', new Body([]), [
                    'arg1',
                    'arg2',
                    'arg3',
                ]),
                'expectedString' =>
                    'public function emptyPublicMethod($arg1, $arg2, $arg3)' . "\n" .
                    '{' . "\n\n" .
                    '}'
            ],
            'public, no arguments, has return type, no lines' => [
                'methodDefinition' => $emptyMethodWithReturnType,
                'expectedString' =>
                    'public function emptyPublicMethodWithReturnType(): string' . "\n" .
                    '{' . "\n\n" .
                    '}'
            ],
            'public, has arguments, no return type, has lines' => [
                'methodDefinition' => new MethodDefinition(
                    'nameOfMethod',
                    new Body([
                        new SingleLineComment('Assign object method call to $value'),
                        new EmptyLine(),
                        new AssignmentStatement(
                            new VariableName('value'),
                            new ObjectMethodInvocation(
                                new VariableDependency('OBJECT'),
                                'methodName',
                                [
                                    new LiteralExpression('$x'),
                                    new LiteralExpression('$y'),
                                ]
                            )
                        ),
                    ]),
                    ['x', 'y']
                ),
                'expectedString' =>
                    'public function nameOfMethod($x, $y)' . "\n" .
                    '{' . "\n" .
                    '    // Assign object method call to $value' . "\n" .
                    "\n" .
                    '    $value = {{ OBJECT }}->methodName($x, $y);' . "\n" .
                    '}'
            ],
            'public, has arguments, no return type, has lines with trailing newline' => [
                'methodDefinition' => new MethodDefinition(
                    'nameOfMethod',
                    new Body([
                        new SingleLineComment('comment'),
                        new EmptyLine(),
                    ]),
                    ['x', 'y']
                ),
                'expectedString' =>
                    'public function nameOfMethod($x, $y)' . "\n" .
                    '{' . "\n" .
                    '    // comment' . "\n" .
                    '}'
            ],
            'public static, no arguments, no return type, no lines' => [
                'methodDefinition' => $emptyPublicStaticMethod,
                'expectedString' =>
                    'public static function emptyPublicStaticMethod()' . "\n" .
                    '{' . "\n\n" .
                    '}'
            ],
            'public, has arguments, no return type, has lines, with docblock' => [
                'methodDefinition' => $this->createMethodDefinitionWithDocBlock(
                    new MethodDefinition(
                        'nameOfMethod',
                        new Body([
                            new SingleLineComment('Assign object method call to $value'),
                            new EmptyLine(),
                            new AssignmentStatement(
                                new VariableName('value'),
                                new ObjectMethodInvocation(
                                    new VariableDependency('OBJECT'),
                                    'methodName',
                                    [
                                        new LiteralExpression('$x'),
                                        new LiteralExpression('$y'),
                                    ]
                                )
                            ),
                        ]),
                        ['x', 'y']
                    ),
                    new DocBlock([
                        '@dataProvider nameOfMethodDataProvider',
                    ])
                ),
                'expectedString' =>
                    '/**' . "\n" .
                    ' * @dataProvider nameOfMethodDataProvider' . "\n" .
                    ' */' . "\n" .
                    'public function nameOfMethod($x, $y)' . "\n" .
                    '{' . "\n" .
                    '    // Assign object method call to $value' . "\n" .
                    "\n" .
                    '    $value = {{ OBJECT }}->methodName($x, $y);' . "\n" .
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
}
