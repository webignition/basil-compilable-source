<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

class Renderable implements RenderableInterface
{
    private string $template;
    private array $context;

    public function __construct(string $template, array $context = [])
    {
        $this->template = $template;
        $this->context = $context;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
