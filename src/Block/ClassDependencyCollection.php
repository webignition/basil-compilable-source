<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\LineInterface;

class ClassDependencyCollection extends AbstractBlock
{
    public function render(): string
    {
        $renderedContent = parent::render();

        $renderedLines = explode("\n", $renderedContent);
        sort($renderedLines);

        return trim(implode("\n", $renderedLines));
    }

    public function canLineBeAdded(LineInterface $line): bool
    {
        if ($line instanceof ClassDependency) {
            return false === $this->containsClassDependency($line);
        }

        return false;
    }

    public function merge(ClassDependencyCollection $collection): ClassDependencyCollection
    {
        return new ClassDependencyCollection(array_merge($this->getLines(), $collection->getLines()));
    }

    private function containsClassDependency(ClassDependency $classDependency): bool
    {
        $renderedClassDependency = $classDependency->render();

        foreach ($this as $line) {
            /* @var ClassDependency $line */
            $renderedLine = $line->render();

            if ($renderedLine === $renderedClassDependency) {
                return true;
            }
        }

        return false;
    }
}
