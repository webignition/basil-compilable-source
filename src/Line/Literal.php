<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\AbstractStringLine;
use webignition\BasilCompilableSource\LineInterface;

class Literal extends AbstractStringLine implements LineInterface
{
    protected function getRenderPattern(): string
    {
        return '%s';
    }
}
