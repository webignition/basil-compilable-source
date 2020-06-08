<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\LineInterface;

interface StatementInterface extends BodyContentInterface, HasMetadataInterface, LineInterface
{
    public function getExpression(): ExpressionInterface;
}
