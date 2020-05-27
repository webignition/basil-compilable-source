<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

abstract class AbstractStringLine implements SourceInterface
{
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    abstract protected function getRenderPattern(): string;

    public function getContent(): string
    {
        return $this->content;
    }

    public function render(): string
    {
        return sprintf($this->getRenderPattern(), $this->content);
    }
}
