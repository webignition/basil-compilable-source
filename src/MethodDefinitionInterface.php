<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\DocBlock\DocBlock;

interface MethodDefinitionInterface extends HasMetadataInterface, SourceInterface
{
    /**
     * @return string[]
     */
    public function getArguments(): array;
    public function getName(): string;
    public function getReturnType(): ?string;
    public function getVisibility(): string;
    public function isStatic(): bool;
    public function getDocBlock(): ?DocBlock;
    public function withDocBlock(DocBlock $docBlock): MethodDefinitionInterface;
}
