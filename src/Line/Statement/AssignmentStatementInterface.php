<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\VariablePlaceholder;

interface AssignmentStatementInterface extends StatementInterface
{
    public function getVariablePlaceholder(): VariablePlaceholder;
    public function getCastTo(): ?string;
}
