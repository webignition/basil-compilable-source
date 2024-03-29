<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\TypeDeclaration;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\TypeDeclarationInterface;

class ObjectTypeDeclarationTest extends TestCase
{
    public function testGetMetadata(): void
    {
        $type = new ClassName(\Exception::class);
        $declaration = new ObjectTypeDeclaration($type);

        $expectedMetadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                $type,
            ]),
        ]);

        $this->assertEquals($expectedMetadata, $declaration->getMetadata());
    }

    /**
     * @dataProvider toStringDataProvider
     */
    public function testToString(ObjectTypeDeclaration $declaration, string $expectedString): void
    {
        $this->assertSame($expectedString, (string) $declaration);
    }

    /**
     * @return array<mixed>
     */
    public function toStringDataProvider(): array
    {
        return [
            'class in root namespace' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassName(\Exception::class)
                ),
                'expectedString' => '\Exception',
            ],
            'interface in root namespace' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassName(\Traversable::class)
                ),
                'expectedString' => '\Traversable',
            ],
            'class not in root namespace' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassName(ObjectTypeDeclaration::class)
                ),
                'expectedString' => 'ObjectTypeDeclaration',
            ],
            'interface not in root namespace' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassName(TypeDeclarationInterface::class)
                ),
                'expectedString' => 'TypeDeclarationInterface',
            ],
            'class not in root namespace, has alias' => [
                'declaration' => new ObjectTypeDeclaration(
                    new ClassName(ObjectTypeDeclaration::class, 'AliasName')
                ),
                'expectedString' => 'AliasName',
            ],
        ];
    }
}
