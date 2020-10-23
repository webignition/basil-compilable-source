<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\SourceInterface;
use webignition\StubbleResolvable\ResolvableInterface;

interface ExpressionInterface extends HasMetadataInterface, SourceInterface, ResolvableInterface
{
}
