<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\TryCatch;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class CatchBlockTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMetadata()
    {
        $body = new Body([
            new AssignmentStatement(
                new VariableDependency('DEPENDENCY'),
                new StaticObjectMethodInvocation(
                    new StaticObject(\RuntimeException::class),
                    'staticMethodName'
                )
            ),
        ]);

        $catchBlock = new CatchBlock(
            new CatchExpression(
                new ObjectTypeDeclarationCollection([
                    new ObjectTypeDeclaration(new ClassDependency(\Exception::class)),
                ])
            ),
            $body
        );

        $expectedMetadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassDependency(\RuntimeException::class),
                new ClassDependency(\Exception::class),
            ]),
            Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                'DEPENDENCY',
            ]),
        ]);

        $this->assertEquals($expectedMetadata, $catchBlock->getMetadata());
    }

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
            'single-class expression' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassDependency(\Exception::class)),
                        ])
                    ),
                    new Statement(
                        new LiteralExpression('"literal"')
                    )
                ),
                'expectedString' =>
                    'catch (Exception $exception) {' . "\n" .
                    '    "literal";' . "\n" .
                    '}',
            ],
            'multi-class expression' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassDependency(\LogicException::class)),
                            new ObjectTypeDeclaration(new ClassDependency(\RuntimeException::class)),
                        ])
                    ),
                    new Statement(
                        new LiteralExpression('"literal"')
                    )
                ),
                'expectedString' =>
                    'catch (LogicException | RuntimeException $exception) {' . "\n" .
                    '    "literal";' . "\n" .
                    '}',
            ],
        ];
    }
}
