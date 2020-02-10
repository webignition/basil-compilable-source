<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

interface SourceInterface
{
    public function render(): string;
}
