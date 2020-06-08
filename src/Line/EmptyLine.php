<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\BasilCompilableSource\LineInterface;

class EmptyLine implements BodyContentInterface, LineInterface
{
    public function render(): string
    {
        return '';
    }
}
