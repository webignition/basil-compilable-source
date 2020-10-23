<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class VariableDependency implements ExpressionInterface, VariableDependencyInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE = '{{ {{ name }} }}';

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMetadata(): MetadataInterface
    {
        $placeholderCollection = new VariableDependencyCollection();
        $placeholderCollection->add($this);

        return new Metadata([
            Metadata::KEY_VARIABLE_DEPENDENCIES => $placeholderCollection,
        ]);
    }

    public function getTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    public function getContext(): array
    {
        return             [
            'name' => $this->name,
        ];
    }
}
