<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\AbstractStringLine;
use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\BasilCompilableSource\LineInterface;

class SingleLineComment extends AbstractStringLine implements BodyContentInterface, LineInterface
{
    protected function getRenderPattern(): string
    {
        return '// %s';
    }
}
