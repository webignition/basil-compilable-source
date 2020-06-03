<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\ResolvingPlaceholder;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;

class CatchExpression implements ExpressionInterface
{
    private ObjectTypeDeclarationCollection $classes;

    public function __construct(ObjectTypeDeclarationCollection $classes)
    {
        $this->classes = $classes;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();
        $metadata = $metadata->merge($this->classes->getMetadata());

        return $metadata;
    }

    public function render(): string
    {
        $placeholder = new ResolvingPlaceholder('exception');

        return sprintf(
            '%s %s',
            $this->classes->render(),
            $placeholder->render(),
        );
    }
}
