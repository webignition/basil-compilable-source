<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderTrait;

class LiteralExpression extends AbstractExpression
{
    use RenderTrait;

    private string $content;

    public function __construct(string $content)
    {
        parent::__construct();

        $this->content = $content;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
