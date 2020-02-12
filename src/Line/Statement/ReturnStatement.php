<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

class ReturnStatement extends Statement
{
    private const RENDER_PATTERN = 'return %s';

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            parent::render()
        );
    }
}
