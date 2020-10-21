<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;

class VariableDependency implements ExpressionInterface, RenderableInterface, VariableDependencyInterface
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

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'name' => $this->name,
            ]
        );
    }
}
