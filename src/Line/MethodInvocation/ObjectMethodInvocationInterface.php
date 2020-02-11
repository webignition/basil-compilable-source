<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

use webignition\BasilCompilableSource\VariablePlaceholder;

interface ObjectMethodInvocationInterface extends MethodInvocationInterface
{
    public function getObjectPlaceholder(): VariablePlaceholder;
}
