<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodArguments;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\StubbleResolvable\ResolvableInterface;

interface MethodArgumentsInterface extends HasMetadataInterface, ResolvableInterface
{
    /**
     * @return ExpressionInterface[]
     */
    public function getArguments(): array;
    public function getFormat(): string;
}
