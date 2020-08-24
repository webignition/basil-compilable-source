<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;

interface MethodInvocationInterface extends ExpressionInterface
{
    public function getMethodName(): string;

    /**
     * @return ExpressionInterface[]
     */
    public function getArguments(): array;

    public function getArgumentFormat(): string;

    public function enableErrorSuppression(): void;
    public function disableErrorSuppression(): void;

    public function withInlineArguments(): self;
    public function withStackedArguments(): self;
}
