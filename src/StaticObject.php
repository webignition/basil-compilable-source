<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Expression\AbstractExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;

class StaticObject extends AbstractExpression
{
    private string $object;

    public function __construct(string $object)
    {
        $metadata = null;

        if (ClassName::isFullyQualifiedClassName($object)) {
            $classDependency = new ClassName($object);
            $object = $classDependency->getClass();

            $metadata = new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    $classDependency,
                ]),
            ]);
        }

        parent::__construct($metadata);

        $this->object = $object;
    }

    public function render(): string
    {
        return $this->object;
    }
}
