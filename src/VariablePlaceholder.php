<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

class VariablePlaceholder extends AbstractStringLine implements LineInterface
{
    protected function getRenderPattern(): string
    {
        return '{{ %s }}';
    }
}
