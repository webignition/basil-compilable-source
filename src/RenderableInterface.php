<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

interface RenderableInterface
{
    public function getRenderSource(): RenderSourceInterface;
}
