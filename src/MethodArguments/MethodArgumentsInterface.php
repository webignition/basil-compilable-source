<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodArguments;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;

interface MethodArgumentsInterface
{
    /**
     * @return ExpressionInterface[]
     */
    public function getArguments(): array;
    public function getFormat(): string;
}
