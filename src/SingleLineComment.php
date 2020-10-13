<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;

class SingleLineComment implements BodyContentInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '// {{ content }}';

    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    /**
     * @return array<string, string>
     */
    protected function getRenderContext(): array
    {
        return [
            'content' => $this->content,
        ];
    }
}
