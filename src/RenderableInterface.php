<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\StubbleResolvable\ResolvableInterface;

interface RenderableInterface
{
    public function getResolvable(): ResolvableInterface;
}
