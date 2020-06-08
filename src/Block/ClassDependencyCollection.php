<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSource\SourceInterface;

class ClassDependencyCollection implements SourceInterface
{
    /**
     * @var ClassDependency[]
     */
    private array $dependencies = [];

    /**
     * @param ClassDependency[] $dependencies
     */
    public function __construct(array $dependencies = [])
    {
        foreach ($dependencies as $dependency) {
            if ($dependency instanceof ClassDependency) {
                if (!$this->containsClassDependency($dependency)) {
                    $this->dependencies[] = $dependency;
                }
            }
        }
    }

    public function render(): string
    {
        $renderedDependencies = [];
        foreach ($this->dependencies as $dependency) {
            $renderedDependencies[] = $dependency->render();
        }

        sort($renderedDependencies);

        return trim(implode("\n", $renderedDependencies));
    }

    public function merge(ClassDependencyCollection $collection): ClassDependencyCollection
    {
        return new ClassDependencyCollection(array_merge($this->dependencies, $collection->dependencies));
    }

    private function containsClassDependency(ClassDependency $classDependency): bool
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
