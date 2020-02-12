<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

interface ClassDefinitionInterface extends SourceInterface
{
    public function getName(): string;
    public function getBaseClass(): ?ClassDependency;
    public function getMetadata(): MetadataInterface;

    /**
     * @return MethodDefinitionInterface[]
     */
    public function getMethods(): array;
}
