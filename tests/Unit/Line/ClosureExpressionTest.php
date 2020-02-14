<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Block\CodeBlock;
use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Line\ClosureExpression;
use webignition\BasilCompilableSource\Line\CompositeExpression;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\ReturnStatement;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class ClosureExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(CodeBlockInterface $codeBlock, MetadataInterface $expectedMetadata)
    {
        $expression = new ClosureExpression($codeBlock);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
        $this->assertNull($expression->getCastTo());
        $this->assertSame($codeBlock, $expression->getCodeBlock());
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
                        VariablePlaceholder::createExport('EXPORT'),
                        new ObjectMethodInvocation(
                            VariablePlaceholder::createDependency('DEPENDENCY'),
                            'dependencyMethodName'
                        )
                    ),
                    new ReturnStatement(
                        new CompositeExpression([
                            new ObjectMethodInvocation(
                                VariablePlaceholder::createExport('EXPORT'),
                                'getWidth',
                                [],
                                MethodInvocation::ARGUMENT_FORMAT_INLINE,
                                'string'
                            ),
                            new LiteralExpression(' . \'x\' . '),
                            new ObjectMethodInvocation(
                                VariablePlaceholder::createExport('EXPORT'),
                                'getHeight',
                                [],
                                MethodInvocation::ARGUMENT_FORMAT_INLINE,
                                'string'
                            ),
                        ])
                    )
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'EXPORT',
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
                        new ReturnStatement(new LiteralExpression('5', 'string')),
                    ])
                ),
                'expectedString' =>
                    '(function () {' . "\n" .
                    '    return (string) 5;' . "\n" .
                    '})()',
            ],
            'multiple literal statements' => [
                'expression' => new ClosureExpression(
                    new CodeBlock([
                        new Statement(new LiteralExpression('3')),
                        new Statement(new LiteralExpression('4')),
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
                            VariablePlaceholder::createExport('EXPORT'),
                            new ObjectMethodInvocation(
                                VariablePlaceholder::createDependency('DEPENDENCY'),
                                'dependencyMethodName'
                            )
                        ),
                        new ReturnStatement(
                            new CompositeExpression([
                                new ObjectMethodInvocation(
                                    VariablePlaceholder::createExport('EXPORT'),
                                    'getWidth',
                                    [],
                                    MethodInvocation::ARGUMENT_FORMAT_INLINE,
                                    'string'
                                ),
                                new LiteralExpression(' . \'x\' . '),
                                new ObjectMethodInvocation(
                                    VariablePlaceholder::createExport('EXPORT'),
                                    'getHeight',
                                    [],
                                    MethodInvocation::ARGUMENT_FORMAT_INLINE,
                                    'string'
                                ),
                            ])
                        )
                    ])
                ),
                '(function () {' . "\n" .
                '    {{ EXPORT }} = {{ DEPENDENCY }}->dependencyMethodName();' . "\n" .
                "\n" .
                '    return (string) {{ EXPORT }}->getWidth() . \'x\' . (string) {{ EXPORT }}->getHeight();' . "\n" .
                '})()',
            ],
        ];
    }
}
