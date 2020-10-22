<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;

class SingleLineComment implements BodyContentInterface, ResolvableProviderInterface
{
    use RenderTrait;

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
