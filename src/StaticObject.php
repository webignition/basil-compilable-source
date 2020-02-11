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
    private $classDependency;

    public function __construct(ClassDependency $classDependency)
    {
        parent::__construct($classDependency->getClass());

        $this->classDependency = $classDependency;
    }

    protected function getRenderPattern(): string
    {
        return '%s';
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                $this->classDependency,
            ]),
        ]);
    }
}
