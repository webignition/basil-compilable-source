<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\LineInterface;

interface StatementInterface extends BodyContentInterface, BodyInterface, LineInterface
{
    public function getExpression(): ExpressionInterface;
}
