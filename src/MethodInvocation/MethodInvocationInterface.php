<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

interface MethodInvocationInterface extends ExpressionInterface
{
    public function getMethodName(): string;
    public function getArguments(): MethodArgumentsInterface;

    public function enableErrorSuppression(): void;
    public function disableErrorSuppression(): void;
}
