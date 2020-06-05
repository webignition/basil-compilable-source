<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class VariableName implements ExpressionInterface, VariablePlaceholderInterface
{
    private const RENDER_PATTERN = '$%s';

    private string $name;
    private MetadataInterface $metadata;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->metadata = new Metadata();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function render(): string
    {
        return sprintf(self::RENDER_PATTERN, $this->name);
    }
}
