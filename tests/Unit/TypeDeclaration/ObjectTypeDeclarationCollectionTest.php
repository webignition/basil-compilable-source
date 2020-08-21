<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\TypeDeclaration;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclaration;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;

class ObjectTypeDeclarationCollectionTest extends TestCase
{
    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ObjectTypeDeclarationCollection $collection, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $collection->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'collection' => new ObjectTypeDeclarationCollection([]),
                'expectedMetadata' => new Metadata(),
            ],
            'non-empty' => [
                'collection' => new ObjectTypeDeclarationCollection([
                    new ObjectTypeDeclaration(new ClassName(\Exception::class)),
                    new ObjectTypeDeclaration(new ClassName(\Traversable::class)),
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(\Exception::class),
                        new ClassName(\Traversable::class),
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectTypeDeclarationCollection $collection, string $expectedString)
    {
        $this->assertSame($expectedString, $collection->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'declaration' => new ObjectTypeDeclarationCollection([]),
                'expectedString' => '',
            ],
            'single' => [
                'declaration' => new ObjectTypeDeclarationCollection([
                    new ObjectTypeDeclaration(new ClassName(\Exception::class)),
                ]),
                'expectedString' => 'Exception',
            ],
            'single with alias' => [
                'declaration' => new ObjectTypeDeclarationCollection([
                    new ObjectTypeDeclaration(new ClassName(\Exception::class, 'AliasName')),
                ]),
                'expectedString' => 'AliasName',
            ],
            'multiple' => [
                'declaration' => new ObjectTypeDeclarationCollection([
                    new ObjectTypeDeclaration(new ClassName(\Exception::class)),
                    new ObjectTypeDeclaration(new ClassName(\Traversable::class)),
                ]),
                'expectedString' => 'Exception | Traversable',
            ],
            'class names are sorted' => [
                'declaration' => new ObjectTypeDeclarationCollection([
                    new ObjectTypeDeclaration(new ClassName(\Exception::class, 'Charlie')),
                    new ObjectTypeDeclaration(new ClassName(\Traversable::class, 'Alpha')),
                    new ObjectTypeDeclaration(new ClassName(ObjectTypeDeclarationCollection::class, 'Bravo')),
                ]),
                'expectedString' => 'Alpha | Bravo | Charlie',
            ],
        ];
    }
}
