<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\Line\ExpressionInterface;

interface AssignmentStatementInterface extends StatementInterface
{
    public function getVariableDependency(): ExpressionInterface;
}
