<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;

class SingleLineComment implements BodyContentInterface
{
    use ResolvableStringableTrait;

    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function __toString(): string
    {
        return '// ' . $this->content;
    }
}
