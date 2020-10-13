<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class VariableName implements ExpressionInterface, RenderableInterface, VariablePlaceholderInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '${{ name }}';

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

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'name' => $this->name,
            ]
        );
    }
}
