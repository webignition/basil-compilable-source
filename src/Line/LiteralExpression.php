<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

class LiteralExpression extends AbstractExpression
{
    private $content;

    public function __construct(string $content)
    {
        parent::__construct();

        $this->content = $content;
    }

    public function render(): string
    {
        return $this->content;
    }
}
