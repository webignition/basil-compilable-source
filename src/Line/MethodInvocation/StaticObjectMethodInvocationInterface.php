<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

use webignition\BasilCompilableSource\StaticObject;

interface StaticObjectMethodInvocationInterface extends MethodInvocationInterface
{
    public function getStaticObject(): StaticObject;
}
