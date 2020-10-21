<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;

class ArrayKey implements RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '\'{{ key }}\'';

    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'key' => $this->key,
            ]
        );
    }
}
