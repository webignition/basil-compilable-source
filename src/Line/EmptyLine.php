<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\LineInterface;

class EmptyLine implements LineInterface
{
    public function render(): string
    {
        return '';
    }
}
