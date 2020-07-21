<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariableName;

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
        return $metadata->merge($this->classes->getMetadata());
    }

    public function render(): string
    {
        return sprintf(
            '%s %s',
            $this->classes->render(),
            (new VariableName('exception'))->render(),
        );
    }
}
