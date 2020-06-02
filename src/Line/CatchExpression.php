<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariablePlaceholder;

class CatchExpression implements ExpressionInterface
{
    private ObjectTypeDeclarationCollection $classes;
    private VariablePlaceholder $exceptionVariable;

    public function __construct(ObjectTypeDeclarationCollection $classes, VariablePlaceholder $exceptionVariable)
    {
        $this->classes = $classes;
        $this->exceptionVariable = $exceptionVariable;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();
        $metadata = $metadata->merge($this->classes->getMetadata());
        $metadata = $metadata->merge($this->exceptionVariable->getMetadata());

        return $metadata;
    }

    public function render(): string
    {
        return sprintf(
            '%s %s',
            $this->classes->render(),
            $this->exceptionVariable->render(),
        );
    }
}
