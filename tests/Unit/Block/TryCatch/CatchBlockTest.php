<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\TryCatch;

use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Line\CatchExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\ReturnStatement;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;

class CatchBlockTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(CatchBlock $tryBlock, string $expectedString)
    {
        $this->assertSame($expectedString, $tryBlock->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'no lines, single-class expression' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassDependency(\Exception::class)),
                        ])
                    )
                ),
                'expectedString' =>
                    'catch (Exception $exception) {' . "\n" .
                    "\n" .
                    '}',
            ],
            'no lines, multi-class expression' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassDependency(\LogicException::class)),
                            new ObjectTypeDeclaration(new ClassDependency(\RuntimeException::class)),
                        ])
                    )
                ),
                'expectedString' =>
                    'catch (LogicException | RuntimeException $exception) {' . "\n" .
                    "\n" .
                    '}',
            ],
            'has lines, single-class expression' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassDependency(\Exception::class)),
                        ])
                    ),
                    [
                        new SingleLineComment('Single line comment'),
                        new ReturnStatement(
                            new LiteralExpression('100')
                        ),
                    ]
                ),
                'expectedString' =>
                    'catch (Exception $exception) {' . "\n" .
                    '    // Single line comment' . "\n" .
                    '    return 100;' . "\n" .
                    '}',
            ],
        ];
    }
}
