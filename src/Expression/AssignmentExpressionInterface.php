<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

interface AssignmentExpressionInterface
{
    public function getVariable(): ExpressionInterface;
    public function getValue(): ExpressionInterface;
}
