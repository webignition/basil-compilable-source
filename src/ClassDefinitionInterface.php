<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

interface ClassDefinitionInterface extends HasMetadataInterface, SourceInterface
{
    public function getSignature(): ClassSignature;

    /**
     * @return MethodDefinitionInterface[]
     */
    public function getMethods(): array;
}
