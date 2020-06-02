<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Block\CodeBlock;
use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Block\DocBlock;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodDefinition;
use webignition\BasilCompilableSource\MethodDefinitionInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class MethodDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $name
     * @param CodeBlockInterface $codeBlock
     * @param string[] $arguments
     */
    public function testCreate(string $name, CodeBlockInterface $codeBlock, array $arguments = [])
    {
        $methodDefinition = new MethodDefinition($name, $codeBlock, $arguments);

        $this->assertSame($name, $methodDefinition->getName());
        $this->assertSame($codeBlock, $methodDefinition->getCodeBlock());
        $this->assertSame($arguments, $methodDefinition->getArguments());
        $this->assertsame(MethodDefinition::VISIBILITY_PUBLIC, $methodDefinition->getVisibility());
        $this->assertNull($methodDefinition->getReturnType());
        $this->assertFalse($methodDefinition->isStatic());
        $this->assertNull($methodDefinition->getDocBlock());
    }

    public function createDataProvider(): array
    {
        return [
            'no arguments' => [
                'name' => 'noArguments',
                'codeBlock' => new CodeBlock(),
            ],
            'empty arguments' => [
                'name' => 'emptyArguments',
                'codeBlock' => new CodeBlock(),
                'arguments' => [],
            ],
            'has arguments' => [
                'name' => 'hasArguments',
                'codeBlock' => new CodeBlock(),
                'arguments' => [
                    'arg1',
                    'arg2',
                ],
            ],
        ];
    }

    public function testAddLine()
    {
        $methodDefinition = new MethodDefinition('name', new CodeBlock());
        $this->assertEquals([], $methodDefinition->getLines());

        $methodDefinition->addLine(new EmptyLine());
        $this->assertEquals(
            [
                new EmptyLine(),
            ],
            $methodDefinition->getLines()
        );

        $methodDefinition->addLine(new SingleLineComment('comment'));
        $this->assertEquals(
            [
                new EmptyLine(),
                new SingleLineComment('comment'),
            ],
            $methodDefinition->getLines()
        );
    }

    public function testIsEmpty()
    {
        $methodDefinition = new MethodDefinition('name', new CodeBlock());
        $this->assertTrue($methodDefinition->isEmpty());

        $methodDefinition->addLine(new SingleLineComment('comment'));
        $this->assertFalse($methodDefinition->isEmpty());
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
                'methodDefinition' => new MethodDefinition('name', new CodeBlock()),
                'expectedMetadata' => new Metadata(),
            ],
            'lines without metadata' => [
                'methodDefinition' => new MethodDefinition('name', new CodeBlock([
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
                ])),
                'expectedMetadata' => new Metadata(),
            ],
            'lines with metadata' => [
                'methodDefinition' => new MethodDefinition('name', new CodeBlock([
                    new Statement(
                        new ObjectMethodInvocation(
                            VariablePlaceholder::createDependency('DEPENDENCY'),
                            'methodName'
                        )
                    ),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new MethodInvocation('methodName')
                    ),
                ])),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'PLACEHOLDER',
                    ]),
                ]),
            ],
        ];
    }

    public function testVisibility()
    {
        $methodDefinition = new MethodDefinition('name', new CodeBlock());
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
        $methodDefinition = new MethodDefinition('name', new CodeBlock());
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
        $methodDefinition = new MethodDefinition('name', new CodeBlock());
        $this->assertFalse($methodDefinition->isStatic());

        $methodDefinition->setStatic();
        $this->assertTrue($methodDefinition->isStatic());
    }

    public function testSetDocBlock()
    {
        $methodDefinition = new MethodDefinition('name', new CodeBlock());
        $this->assertNull($methodDefinition->getDocBlock());

        $docBlock = new DocBlock();
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
        $emptyProtectedMethod = new MethodDefinition('emptyProtectedMethod', new CodeBlock());
        $emptyProtectedMethod->setProtected();

        $emptyPrivateMethod = new MethodDefinition('emptyPrivateMethod', new CodeBlock());
        $emptyPrivateMethod->setPrivate();

        $emptyMethodWithReturnType = new MethodDefinition('emptyPublicMethodWithReturnType', new CodeBlock());
        $emptyMethodWithReturnType->setReturnType('string');

        $emptyPublicStaticMethod = new MethodDefinition('emptyPublicStaticMethod', new CodeBlock());
        $emptyPublicStaticMethod->setStatic();

        return [
            'public, no arguments, no return type, no lines' => [
                'methodDefinition' => new MethodDefinition('emptyPublicMethod', new CodeBlock()),
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
                'methodDefinition' => new MethodDefinition('emptyPublicMethod', new CodeBlock(), [
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
                    new CodeBlock([
                        new SingleLineComment('Assign object method call to VALUE'),
                        new EmptyLine(),
                        new AssignmentStatement(
                            VariablePlaceholder::createExport('VALUE'),
                            new ObjectMethodInvocation(
                                VariablePlaceholder::createDependency('OBJECT'),
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
                    '    // Assign object method call to VALUE' . "\n" .
                    "\n" .
                    '    {{ VALUE }} = {{ OBJECT }}->methodName($x, $y);' . "\n" .
                    '}'
            ],
            'public, has arguments, no return type, has lines with trailing newline' => [
                'methodDefinition' => new MethodDefinition(
                    'nameOfMethod',
                    new CodeBlock([
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
                        new CodeBlock([
                            new SingleLineComment('Assign object method call to VALUE'),
                            new EmptyLine(),
                            new AssignmentStatement(
                                VariablePlaceholder::createExport('VALUE'),
                                new ObjectMethodInvocation(
                                    VariablePlaceholder::createDependency('OBJECT'),
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
                        new SingleLineComment('@dataProvider nameOfMethodDataProvider'),
                    ])
                ),
                'expectedString' =>
                    '/**' . "\n" .
                    ' * @dataProvider nameOfMethodDataProvider' . "\n" .
                    ' */' . "\n" .
                    'public function nameOfMethod($x, $y)' . "\n" .
                    '{' . "\n" .
                    '    // Assign object method call to VALUE' . "\n" .
                    "\n" .
                    '    {{ VALUE }} = {{ OBJECT }}->methodName($x, $y);' . "\n" .
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
