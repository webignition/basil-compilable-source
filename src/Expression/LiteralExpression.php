<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

class LiteralExpression extends AbstractExpression
{
    private string $content;

    public function __construct(string $content)
    {
        parent::__construct();

        $this->content = $content;
    }

    public function render(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
