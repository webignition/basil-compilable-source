<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;

interface AssignmentStatementInterface extends StatementInterface
{
    public function getVariable(): ExpressionInterface;
}
