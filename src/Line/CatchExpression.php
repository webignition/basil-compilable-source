<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\TypeDeclaration\ObjectTypeDeclarationCollection;
use webignition\BasilCompilableSource\VariablePlaceholderInterface;

class CatchExpression implements ExpressionInterface
{
    private ObjectTypeDeclarationCollection $classes;
    private VariablePlaceholderInterface $exceptionVariable;

    public function __construct(
        ObjectTypeDeclarationCollection $classes,
        VariablePlaceholderInterface $exceptionVariable
    ) {
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
