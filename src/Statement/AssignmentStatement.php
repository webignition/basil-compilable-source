<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;

class AssignmentStatement extends AbstractAssignmentStatement implements AssignmentStatementInterface
{
    private ExpressionInterface $variableDependency;

    private function __construct(ExpressionInterface $variableDependency, Statement $valueStatement)
    {
        parent::__construct($valueStatement);

        $this->variableDependency = $variableDependency;
    }

    public static function create(ExpressionInterface $variableDependency, ExpressionInterface $valueExpression): self
    {
        return new AssignmentStatement(
            $variableDependency,
            new Statement($valueExpression)
        );
    }

    public function getVariableDependency(): ExpressionInterface
    {
        return $this->variableDependency;
    }
}
