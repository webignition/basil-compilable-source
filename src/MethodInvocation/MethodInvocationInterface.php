<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

interface MethodInvocationInterface extends InvocableInterface
{
    public function enableErrorSuppression(): void;
    public function disableErrorSuppression(): void;
}
