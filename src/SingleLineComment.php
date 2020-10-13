<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;

class SingleLineComment implements BodyContentInterface, RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '// {{ content }}';

    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'content' => $this->content,
            ]
        );
    }
}
