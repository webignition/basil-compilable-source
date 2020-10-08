<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Construct\ReturnConstruct;

class ReturnStatement extends Statement
{
    private const RENDER_PATTERN = '%s %s';

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            (new ReturnConstruct())->render(),
            parent::render()
        );
    }
}
