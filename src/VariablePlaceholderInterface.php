<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;

interface VariablePlaceholderInterface extends ExpressionInterface
{
    public function getName(): string;
}
