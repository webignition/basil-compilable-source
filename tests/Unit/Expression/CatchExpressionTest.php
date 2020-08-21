<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassDependency;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;

class CatchExpressionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMetadata()
    {
        $typeDeclarationCollection = new ObjectTypeDeclarationCollection([
            new ObjectTypeDeclaration(new ClassDependency(\LogicException::class)),
            new ObjectTypeDeclaration(new ClassDependency(\RuntimeException::class)),
        ]);

        $expression = new CatchExpression($typeDeclarationCollection);

        $expectedMetadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassDependency(\LogicException::class),
                new ClassDependency(\RuntimeException::class),
            ]),
        ]);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
    }

    public function testRender()
    {
        $typeDeclarationCollection = new ObjectTypeDeclarationCollection([
            new ObjectTypeDeclaration(new ClassDependency(\LogicException::class)),
            new ObjectTypeDeclaration(new ClassDependency(\RuntimeException::class)),
        ]);

        $expression = new CatchExpression($typeDeclarationCollection);

        $this->assertSame(
            'LogicException | RuntimeException $exception',
            $expression->render()
        );
    }
}
