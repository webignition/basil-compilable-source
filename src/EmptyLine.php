<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;

class EmptyLine implements BodyContentInterface
{
    public function render(): string
    {
        return '';
    }
}
