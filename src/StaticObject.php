<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\AbstractExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Metadata\Metadata;

class StaticObject extends AbstractExpression
{
    private $object;

    public function __construct(string $object, ?string $castTo = null)
    {
        $metadata = null;

        if (ClassDependency::isFullyQualifiedClassName($object)) {
            $classDependency = new ClassDependency($object);
            $object = $classDependency->getClass();

            $metadata = new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    $classDependency,
                ]),
            ]);
        }

        parent::__construct($castTo, $metadata);

        $this->object = $object;
    }

    public function render(): string
    {
        return $this->object;
    }
}
