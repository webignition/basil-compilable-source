<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\SourceInterface;

class EmptyLine implements SourceInterface
{
    private const RENDER_PATTERN = '// %s;';

    public function render(): string
    {
        return '';
    }
}
