<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\TypeDeclaration;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\ResolvableStringableTrait;

class ObjectTypeDeclaration implements TypeDeclarationInterface
{
    use ResolvableStringableTrait;

    private ClassName $type;
    private MetadataInterface $metadata;

    public function __construct(ClassName $type)
    {
        $this->type = $type;
        $this->metadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                $this->type,
            ]),
        ]);
    }

    public function __toString(): string
    {
        return $this->type->renderClassName();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }
}
