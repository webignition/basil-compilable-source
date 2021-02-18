<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\TryCatch;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Block\TryCatch\CatchBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\AssignmentExpression;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Expression\ReturnExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class CatchBlockTest extends AbstractResolvableTest
{
    public function testGetMetadata(): void
    {
        $body = new Body([
            new Statement(
                new AssignmentExpression(
                    new VariableDependency('DEPENDENCY'),
                    new StaticObjectMethodInvocation(
                        new StaticObject(\RuntimeException::class),
                        'staticMethodName'
                    )
                )
            )
        ]);

        $catchBlock = new CatchBlock(
            new CatchExpression(
                new ObjectTypeDeclarationCollection([
                    new ObjectTypeDeclaration(new ClassName(\Exception::class)),
                ])
            ),
            $body
        );

        $expectedMetadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassName(\RuntimeException::class),
                new ClassName(\Exception::class),
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
    public function testRender(CatchBlock $tryBlock, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $tryBlock);
    }

    /**
     * @return array[]
     */
    public function renderDataProvider(): array
    {
        return [
            'single-class expression, all in root namespace' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassName(\Exception::class)),
                        ])
                    ),
                    new Statement(
                        new LiteralExpression('"literal"')
                    )
                ),
                'expectedString' =>
                    'catch (\Exception $exception) {' . "\n" .
                    '    "literal";' . "\n" .
                    '}',
            ],
            'multi-class expression, all in root namespace' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassName(\LogicException::class)),
                            new ObjectTypeDeclaration(new ClassName(\RuntimeException::class)),
                        ])
                    ),
                    new Statement(
                        new LiteralExpression('"literal"')
                    )
                ),
                'expectedString' =>
                    'catch (\LogicException | \RuntimeException $exception) {' . "\n" .
                    '    "literal";' . "\n" .
                    '}',
            ],
            'multi-class expression, not all in root namespace' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassName(\LogicException::class)),
                            new ObjectTypeDeclaration(new ClassName(\RuntimeException::class)),
                            new ObjectTypeDeclaration(new ClassName(TestCase::class)),
                        ])
                    ),
                    new Statement(
                        new LiteralExpression('"literal"')
                    )
                ),
                'expectedString' =>
                    'catch (\LogicException | \RuntimeException | TestCase $exception) {' . "\n" .
                    '    "literal";' . "\n" .
                    '}',
            ],
            'empty return statement only' => [
                'tryBlock' => new CatchBlock(
                    new CatchExpression(
                        new ObjectTypeDeclarationCollection([
                            new ObjectTypeDeclaration(new ClassName(\Exception::class)),
                        ])
                    ),
                    new Statement(
                        new ReturnExpression()
                    )
                ),
                'expectedString' =>
                    'catch (\Exception $exception) {' . "\n" .
                    '    return;' . "\n" .
                    '}',
            ],
        ];
    }
}
