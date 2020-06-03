<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

interface ResolvableVariablePlaceholderInterface extends HasMetadataInterface, VariablePlaceholderInterface
{
    public function getType(): string;
}
