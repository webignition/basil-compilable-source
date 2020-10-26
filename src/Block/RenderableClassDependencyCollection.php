<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\ClassName;

class RenderableClassDependencyCollection extends ClassDependencyCollection
{
    public function __construct(array $classNames = [])
    {
        $renderableClassNames = array_filter($classNames, function (ClassName $className) {
            if (false === $className->isInRootNamespace()) {
                return true;
            }

            return is_string($className->getAlias());
        });

        parent::__construct($renderableClassNames);
    }
}
