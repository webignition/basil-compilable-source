<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Block\DocBlock;

interface MethodDefinitionInterface extends CodeBlockInterface
{
    /**
     * @return string[]
     */
    public function getArguments(): array;
    public function getName(): string;
    public function getReturnType(): ?string;
    public function getVisibility(): string;
    public function getDocBlock(): ?DocBlock;
    public function isStatic(): bool;
}
