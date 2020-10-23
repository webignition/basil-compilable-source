<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodArguments;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\SourceInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;

interface MethodArgumentsInterface extends HasMetadataInterface, SourceInterface, ResolvableProviderInterface
{
    /**
     * @return ExpressionInterface[]
     */
    public function getArguments(): array;
    public function getFormat(): string;
}
