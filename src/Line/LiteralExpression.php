<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class LiteralExpression implements ExpressionInterface
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata();
    }

    public function render(): string
    {
        return $this->content;
    }
}
