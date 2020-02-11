<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

interface StatementInterface extends ExpressionInterface
{
    public function getExpression(): ExpressionInterface;
}
