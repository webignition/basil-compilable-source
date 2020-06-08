<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\TypeDeclaration;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ObjectTypeDeclaration implements TypeDeclarationInterface
{
    private ClassDependency $type;
    private MetadataInterface $metadata;

    public function __construct(ClassDependency $type)
    {
        $this->type = $type;
        $this->metadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                $this->type,
            ]),
        ]);
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function render(): string
    {
        $alias = $this->type->getAlias();

        return null === $alias
            ? $this->type->getClass()
            : $alias;
    }
}
