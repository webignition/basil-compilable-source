<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

interface StatementInterface extends LineInterface
{
    public function getExpression(): ExpressionInterface;
    public function getMetadata(): MetadataInterface;
}
