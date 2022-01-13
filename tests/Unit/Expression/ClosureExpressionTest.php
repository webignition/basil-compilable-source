<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryCatchBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\AssignmentExpression;
use webignition\BasilCompilableSource\Expression\CastExpression;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Expression\ClosureExpression;
use webignition\BasilCompilableSource\Expression\CompositeExpression;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Expression\ReturnExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\SingleLineComment;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;

class ClosureExpressionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(BodyInterface $body, MetadataInterface $expectedMetadata): void
    {
        $expression = new ClosureExpression($body);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
    }

    /**
     * @return array[]
     */
    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'body' => new Body([]),
                'expectedMetadata' => new Metadata(),
            ],
            'non-empty, no metadata' => [
                'body' => new Body([
                    new Statement(new LiteralExpression('5')),
                    new Statement(new LiteralExpression('"string"')),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'non-empty, has metadata' => [
                'body' => new Body([
                    new Statement(
                        new AssignmentExpression(
                            new VariableName('variable'),
                            new ObjectMethodInvocation(
                                new VariableDependency('DEPENDENCY'),
                                'dependencyMethodName'
                            )
                        )
                    ),
                    new Statement(
                        new ReturnExpression(
                            new CompositeExpression([
                                new CastExpression(
                                    new ObjectMethodInvocation(
                                        new VariableName('variable'),
                                        'getWidth'
                                    ),
                                    'string'
                                ),
                                new LiteralExpression(' . \'x\' . '),
                                new CastExpression(
                                    new ObjectMethodInvocation(
                                        new VariableName('variable'),
                                        'getHeight'
                                    ),
                                    'string'
                                ),
                            ])
                        )
                    ),
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
    public function testRender(ClosureExpression $expression, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    /**
     * @return array[]
     */
    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'expression' => new ClosureExpression(new Body([])),
                'expectedString' => '(function () {' . "\n" .
                    '' . "\n" .
                    '})()',
            ],
            'single literal statement' => [
                'expression' => new ClosureExpression(
                    new Body([
                        new Statement(
                            new ReturnExpression(new LiteralExpression('5'))
                        ),
                    ])
                ),
                'expectedString' => '(function () {' . "\n" .
                    '    return 5;' . "\n" .
                    '})()',
            ],
            'single literal statement, with return statement expression cast to string' => [
                'expression' => new ClosureExpression(
                    new Body([
                        new Statement(
                            new ReturnExpression(
                                new CastExpression(
                                    new LiteralExpression('5'),
                                    'string'
                                )
                            )
                        ),
                    ])
                ),
                'expectedString' => '(function () {' . "\n" .
                    '    return (string) (5);' . "\n" .
                    '})()',
            ],
            'multiple literal statements' => [
                'expression' => new ClosureExpression(
                    new Body([
                        new Statement(new LiteralExpression('3')),
                        new Statement(new LiteralExpression('4')),
                        new \webignition\BasilCompilableSource\EmptyLine(),
                        new Statement(
                            new ReturnExpression(new LiteralExpression('5'))
                        ),
                    ])
                ),
                'expectedString' => '(function () {' . "\n" .
                    '    3;' . "\n" .
                    '    4;' . "\n" .
                    "\n" .
                    '    return 5;' . "\n" .
                    '})()',
            ],
            'non-empty, has metadata' => [
                'expression' => new ClosureExpression(
                    new Body([
                        new Statement(
                            new AssignmentExpression(
                                new VariableName('variable'),
                                new ObjectMethodInvocation(
                                    new VariableDependency('DEPENDENCY'),
                                    'dependencyMethodName'
                                )
                            )
                        ),
                        new \webignition\BasilCompilableSource\EmptyLine(),
                        new Statement(
                            new ReturnExpression(
                                new CompositeExpression([
                                    new CastExpression(
                                        new ObjectMethodInvocation(
                                            new VariableName('variable'),
                                            'getWidth'
                                        ),
                                        'string'
                                    ),
                                    new LiteralExpression(' . \'x\' . '),
                                    new CastExpression(
                                        new ObjectMethodInvocation(
                                            new VariableName('variable'),
                                            'getHeight'
                                        ),
                                        'string'
                                    ),
                                ])
                            )
                        ),
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
                                    new ObjectTypeDeclaration(new ClassName(\RuntimeException::class))
                                ])
                            ),
                            new Body([
                                new SingleLineComment('CatchBlock comment'),
                            ])
                        )
                    )
                ),
                'expectedString' => '(function () {' . "\n" .
                    '    try {' . "\n" .
                    '        // TryBlock comment' . "\n" .
                    '    } catch (\RuntimeException $exception) {' . "\n" .
                    '        // CatchBlock comment' . "\n" .
                    '    }' . "\n" .
                    '})()',
            ],
            'with resolving placeholder' => [
                'expression' => new ClosureExpression(
                    new Body([
                        new Statement(
                            new AssignmentExpression(
                                new VariableName('variableName'),
                                new LiteralExpression('"literal value"')
                            )
                        ),
                        new \webignition\BasilCompilableSource\EmptyLine(),
                        new Statement(
                            new ReturnExpression(
                                new VariableName('variableName')
                            )
                        ),
                    ])
                ),
                'expectedString' => '(function () {' . "\n" .
                    '    $variableName = "literal value";' . "\n" .
                    "\n" .
                    '    return $variableName;' . "\n" .
                    '})()',
            ],
        ];
    }
}
