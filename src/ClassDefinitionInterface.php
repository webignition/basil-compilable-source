<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

interface ClassDefinitionInterface extends HasMetadataInterface, SourceInterface
{
    public function getName(): string;
    public function getBaseClass(): ?ClassName;

    /**
     * @return MethodDefinitionInterface[]
     */
    public function getMethods(): array;
}
