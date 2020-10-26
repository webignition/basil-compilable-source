<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\StubbleResolvable\ResolvableInterface;

class EmptyLine implements BodyContentInterface, ResolvableInterface
{
    use ResolvableStringableTrait;
    use RenderTrait;

    public function __toString(): string
    {
        return '';
    }
}
