<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\Stubble\Resolvable;
use webignition\Stubble\ResolvableInterface;

class SingleLineComment implements BodyContentInterface, RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '// {{ content }}';

    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'content' => $this->content,
            ]
        );
    }
}
