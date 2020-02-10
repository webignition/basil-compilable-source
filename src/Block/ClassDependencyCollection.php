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

        return trim($renderedContent);
    }

    protected function canLineBeAdded(LineInterface $line): bool
    {
        if ($line instanceof ClassDependency) {
            return false === $this->containsClassDependency($line);
        }

        return false;
    }

    private function containsClassDependency(ClassDependency $classDependency): bool
    {
        $renderedClassDependency = $classDependency->render();

        foreach ($this->getLines() as $line) {
            /* @var ClassDependency $line */
            $renderedLine = $line->render();

            if ($renderedLine === $renderedClassDependency) {
                return true;
            }
        }

        return false;
    }
}
