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
            $className = new ClassName($object);
            $object = $className->renderClassName();

            $metadata = new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    $className,
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
