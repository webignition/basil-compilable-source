<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;

class CatchExpressionTest extends AbstractResolvableTest
{
    public function testGetMetadata(): void
    {
        $typeDeclarationCollection = new ObjectTypeDeclarationCollection([
            new ObjectTypeDeclaration(new ClassName(\LogicException::class)),
            new ObjectTypeDeclaration(new ClassName(\RuntimeException::class)),
        ]);

        $expression = new CatchExpression($typeDeclarationCollection);

        $expectedMetadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassName(\LogicException::class),
                new ClassName(\RuntimeException::class),
            ]),
        ]);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
    }

    public function testRender(): void
    {
        $typeDeclarationCollection = new ObjectTypeDeclarationCollection([
            new ObjectTypeDeclaration(new ClassName(\LogicException::class)),
            new ObjectTypeDeclaration(new ClassName(\RuntimeException::class)),
        ]);

        $expression = new CatchExpression($typeDeclarationCollection);

        $this->assertRenderResolvable('\LogicException | \RuntimeException $exception', $expression);
    }
}
