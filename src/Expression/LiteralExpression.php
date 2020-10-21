<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderFromTemplateTrait;

class LiteralExpression extends AbstractExpression
{
    use RenderFromTemplateTrait;

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
