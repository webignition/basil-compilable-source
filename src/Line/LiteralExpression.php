<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

class LiteralExpression extends AbstractExpression
{
    private $content;

    public function __construct(string $content, ?string $castTo = null)
    {
        parent::__construct($castTo);

        $this->content = $content;
    }

    public function render(): string
    {
        return parent::render() . $this->content;
    }
}
