<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\StaticObject;

interface StaticObjectMethodInvocationInterface extends MethodInvocationInterface
{
    public function getStaticObject(): StaticObject;
}
