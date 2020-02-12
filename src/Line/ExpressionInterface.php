<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

interface ExpressionInterface extends LineInterface
{
    public function getMetadata(): MetadataInterface;
    public function getCastTo(): ?string;
}
