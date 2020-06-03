<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\LineInterface;

interface StatementInterface extends HasMetadataInterface, LineInterface
{
    public function getExpression(): ExpressionInterface;
}
