<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Construct;

class ReturnConstruct
{
    public function __toString(): string
    {
        return 'return';
    }
}
