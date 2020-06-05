<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\CatchExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariableDependencyCollection;

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
