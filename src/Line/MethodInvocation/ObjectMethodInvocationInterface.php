<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

interface ObjectMethodInvocationInterface extends MethodInvocationInterface
{
    public function getObject(): string;
}
