<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class StaticObject extends AbstractStringLine implements ExpressionInterface
{
    private $metadata;

    public function __construct(string $object)
    {
        if (ClassDependency::isFullyQualifiedClassName($object)) {
            $classDependency = new ClassDependency($object);

            parent::__construct($classDependency->getClass());
            $this->metadata = new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    $classDependency,
                ]),
            ]);
        } else {
            parent::__construct($object);
            $this->metadata = new Metadata();
        }
    }

    protected function getRenderPattern(): string
    {
        return '%s';
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }
}
