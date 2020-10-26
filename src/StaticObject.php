<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class StaticObject implements ExpressionInterface
{
    use ResolvableStringableTrait;

    private string $object;

    public function __construct(string $object)
    {
        $this->object = $object;
    }

    public function getMetadata(): MetadataInterface
    {
        if (ClassName::isFullyQualifiedClassName($this->object)) {
            return new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    new ClassName($this->object),
                ]),
            ]);
        }

        return new Metadata();
    }

    public function render(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        if (ClassName::isFullyQualifiedClassName($this->object)) {
            $className = new ClassName($this->object);
            return $className->renderClassName();
        }

        return $this->object;
    }
}
