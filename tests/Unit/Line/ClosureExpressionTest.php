<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Block\CodeBlock;
use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryCatchBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Line\CastExpression;
use webignition\BasilCompilableSource\Line\CatchExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\ClosureExpression;
use webignition\BasilCompilableSource\Line\CompositeExpression;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\ReturnStatement;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableName;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class ClosureExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(CodeBlockInterface $codeBlock, MetadataInterface $expectedMetadata)
    {
        $expression = new ClosureExpression($codeBlock);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'codeBlock' => new CodeBlock(),
                'expectedMetadata' => new Metadata(),
            ],
            'non-empty, no metadata' => [
                'codeBlock' => new CodeBlock([
                    new Statement(new LiteralExpression('5')),
                    new Statement(new LiteralExpression('"string"')),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'non-empty, has metadata' => [
                'codeBlock' => new CodeBlock([
                    new AssignmentStatement(
                        new VariableName('variable'),
                        new ObjectMethodInvocation(
                            new VariableDependency('DEPENDENCY'),
                            'dependencyMethodName'
                        )
                    ),
                    new ReturnStatement(
                        new CompositeExpression([
                            new CastExpression(
                                new ObjectMethodInvocation(
                                    new VariableName('variable'),
                                    'getWidth',
                                    [],
                                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                                ),
                                'string'
                            ),
                            new LiteralExpression(' . \'x\' . '),
                            new CastExpression(
                                new ObjectMethodInvocation(
                                    new VariableName('variable'),
                                    'getHeight',
                                    [],
                                    MethodInvocation::ARGUMENT_FORMAT_INLINE
                                ),
                                'string'
                            ),
                        ])
                    )
                ]),
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
    public function testRender(ClosureExpression $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'expression' => new ClosureExpression(new CodeBlock()),
                'expectedString' =>
                    '(function () {' . "\n" .
                    '' . "\n" .
                    '})()',
            ],
            'single literal statement' => [
                'expression' => new ClosureExpression(
                    new CodeBlock([
                        new ReturnStatement(new LiteralExpression('5')),
                    ])
                ),
                'expectedString' =>
                    '(function () {' . "\n" .
                    '    return 5;' . "\n" .
                    '})()',
            ],
            'single literal statement, with return statement expression cast to string' => [
                'expression' => new ClosureExpression(
                    new CodeBlock([
                        new ReturnStatement(
                            new CastExpression(
                                new LiteralExpression('5'),
                                'string'
                            )
                        ),
                    ])
                ),
                'expectedString' =>
                    '(function () {' . "\n" .
                    '    return (string) (5);' . "\n" .
                    '})()',
            ],
            'multiple literal statements' => [
                'expression' => new ClosureExpression(
                    new CodeBlock([
                        new Statement(new LiteralExpression('3')),
                        new Statement(new LiteralExpression('4')),
                        new EmptyLine(),
                        new ReturnStatement(new LiteralExpression('5')),
                    ])
                ),
                'expectedString' =>
                    '(function () {' . "\n" .
                    '    3;' . "\n" .
                    '    4;' . "\n" .
                    "\n" .
                    '    return 5;' . "\n" .
                    '})()',
            ],
            'non-empty, has metadata' => [
                'expression' => new ClosureExpression(
                    new CodeBlock([
                        new AssignmentStatement(
                            new VariableName('variable'),
                            new ObjectMethodInvocation(
                                new VariableDependency('DEPENDENCY'),
                                'dependencyMethodName'
                            )
                        ),
                        new EmptyLine(),
                        new ReturnStatement(
                            new CompositeExpression([
                                new CastExpression(
                                    new ObjectMethodInvocation(
                                        new VariableName('variable'),
                                        'getWidth',
                                        [],
                                        MethodInvocation::ARGUMENT_FORMAT_INLINE
                                    ),
                                    'string'
                                ),
                                new LiteralExpression(' . \'x\' . '),
                                new CastExpression(
                                    new ObjectMethodInvocation(
                                        new VariableName('variable'),
                                        'getHeight',
                                        [],
                                        MethodInvocation::ARGUMENT_FORMAT_INLINE
                                    ),
                                    'string'
                                ),
                            ])
                        )
                    ])
                ),
                '(function () {' . "\n" .
                '    $variable = {{ DEPENDENCY }}->dependencyMethodName();' . "\n" .
                "\n" .
                '    return (string) ($variable->getWidth()) . \'x\' . (string) ($variable->getHeight());' . "\n" .
                '})()',
            ],
            'try/catch block' => [
                'expression' => new ClosureExpression(
                    new TryCatchBlock(
                        new TryBlock(
                            new Body([
                                new SingleLineComment('TryBlock comment'),
                            ])
                        ),
                        new CatchBlock(
                            new CatchExpression(
                                new ObjectTypeDeclarationCollection([
                                    new ObjectTypeDeclaration(new ClassDependency(\RuntimeException::class))
                                ])
                            )
                        )
                    )
                ),
                'expectedString' =>
                    '(function () {' . "\n" .
                    '    try {' . "\n" .
                    '        // TryBlock comment' . "\n" .
                    '    } catch (RuntimeException $exception) {' . "\n" .
                    "\n" .
                    '    }' . "\n" .
                    '})()',
            ],
            'with resolving placeholder' => [
                'expression' => new ClosureExpression(
                    new CodeBlock([
                        new AssignmentStatement(
                            new VariableName('variableName'),
                            new LiteralExpression('"literal value"')
                        ),
                        new EmptyLine(),
                        new ReturnStatement(
                            new VariableName('variableName')
                        ),
                    ])
                ),
                'expectedString' =>
                    '(function () {' . "\n" .
                    '    $variableName = "literal value";' . "\n" .
                    "\n" .
                    '    return $variableName;' . "\n" .
                    '})()',
            ],
        ];
    }
}
