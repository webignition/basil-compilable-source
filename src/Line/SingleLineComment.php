<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\SourceInterface;

class SingleLineComment implements SourceInterface
{
    private const RENDER_PATTERN = '// %s;';

    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function render(): string
    {
        return sprintf(self::RENDER_PATTERN, $this->content);
    }
}
