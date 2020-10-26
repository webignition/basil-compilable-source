<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\ResolvableStringableTrait;

class LiteralExpression implements ExpressionInterface
{
    use ResolvableStringableTrait;

    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata();
    }
}
