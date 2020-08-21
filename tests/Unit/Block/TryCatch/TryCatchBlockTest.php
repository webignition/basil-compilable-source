<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\TryCatch;

use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Block\TryCatch\TryCatchBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\SingleLineComment;
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
                                new ObjectTypeDeclaration(new ClassName(\LogicException::class)),
                                new ObjectTypeDeclaration(new ClassName(\RuntimeException::class)),
                            ])
                        ),
                        new Body([
                            new SingleLineComment('handle LogicException and RuntimeException')
                        ]),
                    ),
                    new CatchBlock(
                        new CatchExpression(
                            new ObjectTypeDeclarationCollection([
                                new ObjectTypeDeclaration(new ClassName(\LengthException::class)),
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
                    '} catch (\LogicException | \RuntimeException $exception) {' . "\n" .
                    '    // handle LogicException and RuntimeException' . "\n" .
                    '} catch (\LengthException $exception) {' . "\n" .
                    '    // handle LengthException' . "\n" .
                    '}',
            ],
        ];
    }
}
