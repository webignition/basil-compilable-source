<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;

class SingleLineComment extends AbstractStringLine implements BodyContentInterface
{
    protected function getRenderPattern(): string
    {
        return '// %s';
    }
}
