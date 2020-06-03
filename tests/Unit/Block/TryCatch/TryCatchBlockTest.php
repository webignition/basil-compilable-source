<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\TryCatch;

use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryCatchBlock;
use webignition\BasilCompilableSource\Line\CatchExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariablePlaceholder;

class TryCatchBlockTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(TryCatchBlock $tryCatch, string $expectedString)
    {
        $this->assertSame($expectedString, $tryCatch->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'tryCatch' => new TryCatchBlock(
                    new TryBlock()
                ),
                'expectedString' =>
                    'try {' . "\n" .
                    "\n" .
                    '}',
            ],
            'non-empty' => [
                'tryCatch' => new TryCatchBlock(
                    new TryBlock([
                        new Statement(new MethodInvocation('methodName')),
                    ]),
                    new CatchBlock(
                        new CatchExpression(
                            new ObjectTypeDeclarationCollection([
                                new ObjectTypeDeclaration(new ClassDependency(\LogicException::class)),
                                new ObjectTypeDeclaration(new ClassDependency(\RuntimeException::class)),
                            ]),
                            VariablePlaceholder::createExport('EXCEPTION')
                        ),
                        [
                            new SingleLineComment('handle LogicException and RuntimeException')
                        ]
                    ),
                    new CatchBlock(
                        new CatchExpression(
                            new ObjectTypeDeclarationCollection([
                                new ObjectTypeDeclaration(new ClassDependency(\LengthException::class)),
                            ]),
                            VariablePlaceholder::createExport('L_EXCEPTION')
                        ),
                        [
                            new SingleLineComment('handle LengthException')
                        ]
                    )
                ),
                'expectedString' =>
                    'try {' . "\n" .
                    '    methodName();' . "\n" .
                    '} catch (LogicException | RuntimeException {{ EXCEPTION }}) {' . "\n" .
                    '    // handle LogicException and RuntimeException' . "\n" .
                    '} catch (LengthException {{ L_EXCEPTION }}) {' . "\n" .
                    '    // handle LengthException' . "\n" .
                    '}'
                ,
            ],
        ];
    }
}
