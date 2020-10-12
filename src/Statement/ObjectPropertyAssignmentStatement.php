<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;

class ObjectPropertyAssignmentStatement extends AbstractAssignmentStatement implements AssignmentStatementInterface
{
    private ObjectPropertyAccessExpression $accessExpression;

    private function __construct(ObjectPropertyAccessExpression $accessExpression, Statement $valueStatement)
    {
        parent::__construct($valueStatement);

        $this->accessExpression = $accessExpression;
    }

    public static function createFromExpression(
        ObjectPropertyAccessExpression $accessExpression,
        ExpressionInterface $valueExpression
    ): self {
        return new ObjectPropertyAssignmentStatement(
            $accessExpression,
            new Statement($valueExpression)
        );
    }

    public function getVariableDependency(): ExpressionInterface
    {
        return $this->accessExpression;
    }
}
