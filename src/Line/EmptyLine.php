<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\SourceInterface;

class EmptyLine implements SourceInterface
{
    public function render(): string
    {
        return '';
    }
}
