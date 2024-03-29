<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

interface AssignmentExpressionInterface extends ExpressionInterface
{
    public function getVariable(): ExpressionInterface;

    public function getValue(): ExpressionInterface;

    public function getOperator(): string;
}
