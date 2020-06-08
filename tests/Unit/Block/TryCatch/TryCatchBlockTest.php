<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\TryCatch;

use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryCatchBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Line\CatchExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;

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
            'default' => [
                'tryCatch' => new TryCatchBlock(
                    new TryBlock(
                        new Statement(new MethodInvocation('methodName')),
                    ),
                    new CatchBlock(
                        new CatchExpression(
                            new ObjectTypeDeclarationCollection([
                                new ObjectTypeDeclaration(new ClassDependency(\LogicException::class)),
                                new ObjectTypeDeclaration(new ClassDependency(\RuntimeException::class)),
                            ])
                        ),
                        new Body([
                            new SingleLineComment('handle LogicException and RuntimeException')
                        ]),
                    ),
                    new CatchBlock(
                        new CatchExpression(
                            new ObjectTypeDeclarationCollection([
                                new ObjectTypeDeclaration(new ClassDependency(\LengthException::class)),
                            ])
                        ),
                        new Body([
                            new SingleLineComment('handle LengthException')
                        ])
                    )
                ),
                'expectedString' =>
                    'try {' . "\n" .
                    '    methodName();' . "\n" .
                    '} catch (LogicException | RuntimeException $exception) {' . "\n" .
                    '    // handle LogicException and RuntimeException' . "\n" .
                    '} catch (LengthException $exception) {' . "\n" .
                    '    // handle LengthException' . "\n" .
                    '}'
                ,
            ],
        ];
    }
}
