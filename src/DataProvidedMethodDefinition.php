<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Annotation\DataProviderAnnotation;
use webignition\BasilCompilableSource\DocBlock\DocBlock;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class DataProvidedMethodDefinition implements MethodDefinitionInterface
{
    private const RENDER_TEMPLATE = <<<'EOD'
%s
%s

%s
EOD;

    private MethodDefinitionInterface $methodDefinition;
    private DataProviderMethodDefinitionInterface $dataProviderMethodDefinition;

    public function __construct(
        MethodDefinitionInterface $methodDefinition,
        DataProviderMethodDefinitionInterface $dataProviderMethodDefinition
    ) {
        $this->methodDefinition = $methodDefinition;
        $this->dataProviderMethodDefinition = $dataProviderMethodDefinition;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->methodDefinition->getMetadata();
    }

    public function getArguments(): array
    {
        return $this->methodDefinition->getArguments();
    }

    public function getName(): string
    {
        return $this->methodDefinition->getName();
    }

    public function getReturnType(): ?string
    {
        return $this->methodDefinition->getReturnType();
    }

    public function getVisibility(): string
    {
        return $this->methodDefinition->getVisibility();
    }

    public function isStatic(): bool
    {
        return $this->methodDefinition->isStatic();
    }

    public function createDocBlock(): DocBlock
    {
        return (new DocBlock([
            new DataProviderAnnotation($this->dataProviderMethodDefinition->getName()),
            '',
        ]))->merge(
            $this->methodDefinition->createDocBlock()
        );
    }

    public function render(): string
    {
        $docBlock = $this->createDocBlock();

        return sprintf(
            self::RENDER_TEMPLATE,
            $docBlock->render(),
            $this->methodDefinition->renderMethod(),
            $this->dataProviderMethodDefinition->render()
        );
    }

    public function renderMethod(): string
    {
        return $this->methodDefinition->renderMethod();
    }
}
