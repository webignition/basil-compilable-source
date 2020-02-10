<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class Expression implements ExpressionInterface
{
    private $line;
    private $metadata;

    public function __construct(LineInterface $line, ?MetadataInterface $metadata = null)
    {
        $this->line = $line;
        $this->metadata = $metadata instanceof MetadataInterface ? $metadata : new Metadata();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function render(): string
    {
        return $this->line->render();
    }
}
