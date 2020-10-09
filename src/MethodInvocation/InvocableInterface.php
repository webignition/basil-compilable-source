<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

interface InvocableInterface extends ExpressionInterface
{
    public function getCall(): string;
    public function getArguments(): MethodArgumentsInterface;
}
