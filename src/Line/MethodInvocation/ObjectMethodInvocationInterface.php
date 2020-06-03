<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

use webignition\BasilCompilableSource\VariablePlaceholderInterface;

interface ObjectMethodInvocationInterface extends MethodInvocationInterface
{
    public function getObjectPlaceholder(): VariablePlaceholderInterface;
}
