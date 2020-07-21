<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\TypeDeclaration;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\TypeDeclarationInterface;

class ObjectTypeDeclarationTest extends TestCase
{
    public function testGetMetadata()
    {
        $type = new ClassDependency(\Exception::class);
        $declaration = new ObjectTypeDeclaration($type);

        $expectedMetadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                $type,
            ]),
        ]);

        $this->assertEquals($expectedMetadata, $declaration->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectTypeDeclaration $declaration, string $expectedString)
    {
        $this->assertSame($expectedString, $declaration->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'class in root namespace' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassDependency(\Exception::class)
                ),
                'expectedString' => 'Exception',
            ],
            'interface in root namespace' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassDependency(\Traversable::class)
                ),
                'expectedString' => 'Traversable',
            ],
            'class not in root namespace' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassDependency(ObjectTypeDeclaration::class)
                ),
                'expectedString' => 'ObjectTypeDeclaration',
            ],
            'interface not in root namespace' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassDependency(TypeDeclarationInterface::class)
                ),
                'expectedString' => 'TypeDeclarationInterface',
            ],
            'class not in root namespace, has alias' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassDependency(ObjectTypeDeclaration::class, 'AliasName')
                ),
                'expectedString' => 'AliasName',
            ],
        ];
    }
}
