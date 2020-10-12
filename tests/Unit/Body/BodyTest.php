<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Body;

use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryCatchBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\EmptyLine;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Expression\ClosureExpression;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\SingleLineComment;
use webignition\BasilCompilableSource\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Statement\EmptyReturnStatement;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\ObjectReflector\ObjectReflector;

class BodyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param BodyContentInterface[] $content
     * @param BodyContentInterface[] $expectedContent
     */
    public function testCreate(array $content, array $expectedContent)
    {
        $body = new Body($content);

        $this->assertEquals(
            $expectedContent,
            ObjectReflector::getProperty($body, 'content')
        );
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'content' => [],
                'expectedContent' => [],
            ],
            'has content' => [
                'content' => [
                    new \stdClass(),
                    "\n",
                    new SingleLineComment('singe line comment'),
                    true,
                    new EmptyLine(),
                    1,
                    new Statement(
                        new LiteralExpression('"literal from statement"')
                    ),
                    new Body([
                        new Statement(
                            new LiteralExpression('"literal from statement from body"')
                        )
                    ]),
                    new TryCatchBlock(
                        new TryBlock(
                            new Body([
                                new SingleLineComment('TryBlock comment'),
                            ])
                        ),
                        new CatchBlock(
                            new CatchExpression(
                                new ObjectTypeDeclarationCollection([
                                    new ObjectTypeDeclaration(
                                        new ClassName(\LogicException::class)
                                    )
                                ])
                            ),
                            new Body([
                                new SingleLineComment('CatchBlock comment'),
                            ])
                        )
                    ),
                    new Body([]),
                ],
                'expectedContent' => [
                    new SingleLineComment('singe line comment'),
                    new EmptyLine(),
                    new Statement(
                        new LiteralExpression('"literal from statement"')
                    ),
                    new Body([
                        new Statement(
                            new LiteralExpression('"literal from statement from body"')
                        )
                    ]),
                    new TryCatchBlock(
                        new TryBlock(
                            new Body([
                                new SingleLineComment('TryBlock comment'),
                            ])
                        ),
                        new CatchBlock(
                            new CatchExpression(
                                new ObjectTypeDeclarationCollection([
                                    new ObjectTypeDeclaration(
                                        new ClassName(\LogicException::class)
                                    )
                                ])
                            ),
                            new Body([
                                new SingleLineComment('CatchBlock comment'),
                            ])
                        )
                    ),
                ],
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(Body $body, string $expectedString)
    {
        $this->assertSame($expectedString, $body->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'body' => new Body([]),
                'expectedString' => '',
            ],
            'non-empty' => [
                'body' => new Body([
                    new SingleLineComment('single line comment'),
                    new EmptyLine(),
                    new Statement(
                        new LiteralExpression('"literal from statement"')
                    ),
                    new Body([
                        new Statement(
                            new LiteralExpression('"literal from statement from body"')
                        )
                    ]),
                    new TryCatchBlock(
                        new TryBlock(
                            new Body([
                                new SingleLineComment('TryBlock comment'),
                            ])
                        ),
                        new CatchBlock(
                            new CatchExpression(
                                new ObjectTypeDeclarationCollection([
                                    new ObjectTypeDeclaration(
                                        new ClassName(\LogicException::class)
                                    )
                                ])
                            ),
                            new Body([
                                new SingleLineComment('CatchBlock comment'),
                            ])
                        )
                    ),
                ]),
                'expectedString' =>
                    '// single line comment' . "\n" .
                    "\n" .
                    '"literal from statement";' . "\n" .
                    '"literal from statement from body";' . "\n" .
                    'try {' . "\n" .
                    '    // TryBlock comment' . "\n" .
                    '} catch (\LogicException $exception) {' . "\n" .
                    '    // CatchBlock comment' . "\n" .
                    '}',
            ],
            'empty return only' => [
                'body' => new Body([
                    new EmptyReturnStatement()
                ]),
                'expectedString' => 'return;',
            ],
            'expression and empty return' => [
                'body' => new Body([
                    new Statement(
                        new LiteralExpression('"literal from statement"')
                    ),
                    new EmptyReturnStatement()
                ]),
                'expectedString' =>
                    '"literal from statement";' . "\n" .
                    'return;',
            ],
        ];
    }

    /**
     * @dataProvider createEnclosingBodyDataProvider
     */
    public function testCreateEnclosingBody(BodyInterface $body, BodyInterface $expectedBody)
    {
        $this->assertEquals($expectedBody, Body::createEnclosingBody($body));
    }

    public function createEnclosingBodyDataProvider(): array
    {
        return [
            'enclose a code block' => [
                'body' => new Body([
                    new Statement(
                        new LiteralExpression('"literal')
                    ),
                ]),
                'expectedBody' => new Body([
                    new Statement(
                        new ClosureExpression(
                            new Body([
                                new Statement(
                                    new LiteralExpression('"literal')
                                ),
                            ])
                        )
                    ),
                ]),
            ],
        ];
    }

    public function testCreateFromExpressionsThrowsInvalidArgumentExceptionForNonExpression()
    {
        self::expectExceptionObject(new \InvalidArgumentException('Non-expression at index 1'));

        Body::createFromExpressions([
            new LiteralExpression('"literal one"'),
            true,
            new LiteralExpression('"literal two"'),
        ]);
    }

    /**
     * @dataProvider createFromExpressionsDataProvider
     *
     * @param array<mixed> $expressions
     * @param Body $expectedBody
     */
    public function testCreateFromExpressions(array $expressions, Body $expectedBody)
    {
        self::assertEquals($expectedBody, Body::createFromExpressions($expressions));
    }

    public function createFromExpressionsDataProvider(): array
    {
        return [
            'empty' => [
                'expressions' => [],
                'expectedBody' => new Body([]),
            ],
            'non-empty' => [
                'expressions' => [
                    new LiteralExpression('"literal one"'),
                    new LiteralExpression('"literal two"'),
                ],
                'expectedBody' => new Body([
                    new Statement(
                        new LiteralExpression('"literal one"')
                    ),
                    new Statement(
                        new LiteralExpression('"literal two"')
                    ),
                ]),
            ],
        ];
    }

    public function testCreateForSingleAssignmentStatement()
    {
        $leftHandSide = new VariableDependency('LHS');
        $rightHandSide = new LiteralExpression('"value"');

        $expectedBody = new Body([
            AssignmentStatement::createFromExpression(
                $leftHandSide,
                $rightHandSide
            )
        ]);

        self::assertEquals($expectedBody, Body::createForSingleAssignmentStatement($leftHandSide, $rightHandSide));
    }
}
