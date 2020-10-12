<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;

class AssignmentStatement extends AbstractAssignmentStatement implements AssignmentStatementInterface
{
    public static function create(ExpressionInterface $variableDependency, ExpressionInterface $valueExpression): self
    {
        return new AssignmentStatement(
            $variableDependency,
            new Statement($valueExpression)
        );
    }
}
