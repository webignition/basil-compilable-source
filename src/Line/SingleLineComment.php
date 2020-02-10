<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\AbstractStringLine;
use webignition\BasilCompilableSource\SourceInterface;

class SingleLineComment extends AbstractStringLine implements SourceInterface
{
    protected function getRenderPattern(): string
    {
        return '// %s';
    }
}
