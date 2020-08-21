<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\SourceInterface;

class ClassDependencyCollection implements SourceInterface
{
    /**
     * @var ClassName[]
     */
    private array $dependencies = [];

    /**
     * @param ClassName[] $dependencies
     */
    public function __construct(array $dependencies = [])
    {
        foreach ($dependencies as $dependency) {
            if ($dependency instanceof ClassName) {
                if (!$this->containsClassDependency($dependency)) {
                    $this->dependencies[] = $dependency;
                }
            }
        }
    }

    public function render(): string
    {
        $nonRootNamespaceDependencies = array_filter($this->dependencies, function (ClassName $dependency) {
            return false === $dependency->isInRootNamespace();
        });

        $renderedDependencies = [];
        foreach ($nonRootNamespaceDependencies as $dependency) {
            $renderedDependencies[] = $dependency->render();
        }

        sort($renderedDependencies);

        return trim(implode("\n", $renderedDependencies));
    }

    public function merge(ClassDependencyCollection $collection): ClassDependencyCollection
    {
        return new ClassDependencyCollection(array_merge($this->dependencies, $collection->dependencies));
    }

    private function containsClassDependency(ClassName $classDependency): bool
    {
        $renderedClassDependency = $classDependency->render();

        foreach ($this->dependencies as $dependency) {
            if ($dependency->render() === $renderedClassDependency) {
                return true;
            }
        }

        return false;
    }
}
