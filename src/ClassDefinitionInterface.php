<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Line\ClassDependency;

interface ClassDefinitionInterface extends HasMetadataInterface, SourceInterface
{
    public function getName(): string;
    public function getBaseClass(): ?ClassDependency;

    /**
     * @return MethodDefinitionInterface[]
     */
    public function getMethods(): array;
}
