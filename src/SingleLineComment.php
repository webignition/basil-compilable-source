<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;

class SingleLineComment implements BodyContentInterface
{
    private const RENDER_TEMPLATE = '// %s';

    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function render(): string
    {
        return sprintf(self::RENDER_TEMPLATE, $this->content);
    }
}
