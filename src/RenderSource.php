<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

class RenderSource implements RenderSourceInterface
{
    private string $template;

    /**
     * @var array<string, string>
     */
    private array $context;

    /**
     * @param string $template
     * @param array<string, string> $context
     */
    public function __construct(string $template, array $context = [])
    {
        $this->template = $template;
        $this->context = $context;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array<string, string>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
