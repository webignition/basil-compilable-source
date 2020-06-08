<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Body;

use webignition\BasilCompilableSource\Block\CodeBlock;
use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryCatchBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\BasilCompilableSource\Line\CatchExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\Tests\Services\ObjectReflector;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;

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
                                        new ClassDependency(\LogicException::class)
                                    )
                                ])
                            ),
                            [
                                new CodeBlock([
                                    new SingleLineComment('CatchBlock comment'),
                                ])
                            ]
                        )
                    ),
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
                                        new ClassDependency(\LogicException::class)
                                    )
                                ])
                            ),
                            [
                                new CodeBlock([
                                    new SingleLineComment('CatchBlock comment'),
                                ])
                            ]
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
                                        new ClassDependency(\LogicException::class)
                                    )
                                ])
                            ),
                            [
                                new CodeBlock([
                                    new SingleLineComment('CatchBlock comment'),
                                ])
                            ]
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
                    '} catch (LogicException $exception) {' . "\n" .
                    '    // CatchBlock comment' . "\n" .
                    '}'
                ,
            ],
        ];
    }
}
