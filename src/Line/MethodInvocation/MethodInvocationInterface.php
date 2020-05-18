<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

use webignition\BasilCompilableSource\Line\ExpressionInterface;

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
}
