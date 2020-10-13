<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

interface RenderableInterface
{
    public function getTemplate(): string;

    /**
     * @return array<string, string>
     */
    public function getContext(): array;
}
