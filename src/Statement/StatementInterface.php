<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;

interface StatementInterface extends BodyContentInterface, BodyInterface
{
    public function getExpression(): ExpressionInterface;
}
