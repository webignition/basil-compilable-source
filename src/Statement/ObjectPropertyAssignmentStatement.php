<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;

class ObjectPropertyAssignmentStatement extends AbstractAssignmentStatement implements AssignmentStatementInterface
{
    public static function create(
        ObjectPropertyAccessExpression $accessExpression,
        ExpressionInterface $valueExpression
    ): self {
        return new ObjectPropertyAssignmentStatement(
            $accessExpression,
            new Statement($valueExpression)
        );
    }
}
