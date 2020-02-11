<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\Line\ExpressionInterface;

interface StatementInterface extends ExpressionInterface
{
    public function getExpression(): ExpressionInterface;
}
