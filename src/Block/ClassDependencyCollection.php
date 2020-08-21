<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\UseExpression;
use webignition\BasilCompilableSource\SourceInterface;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\Statement\StatementInterface;

class ClassDependencyCollection implements SourceInterface
{
    /**
     * @var ClassName[]
     */
    private array $classNames = [];

    /**
     * @param ClassName[] $classNames
     */
    public function __construct(array $classNames = [])
    {
        foreach ($classNames as $className) {
            if ($className instanceof ClassName) {
                if (!$this->containsClassName($className)) {
                    $this->classNames[] = $className;
                }
            }
        }
    }

    public function render(): string
    {
        $classNamesToRender = array_filter($this->classNames, function (ClassName $className) {
            if (false === $className->isInRootNamespace()) {
                return true;
            }

            return is_string($className->getAlias());
        });

        $renderedUseStatements = [];
        foreach ($classNamesToRender as $className) {
            $renderedUseStatements[] = $this->createUseStatement($className)->render();
        }

        sort($renderedUseStatements);

        return trim(implode("\n", $renderedUseStatements));
    }

    public function merge(ClassDependencyCollection $collection): ClassDependencyCollection
    {
        return new ClassDependencyCollection(array_merge($this->classNames, $collection->classNames));
    }

    private function containsClassName(ClassName $className): bool
    {
        $renderedClassName = $className->render();

        foreach ($this->classNames as $className) {
            if ($className->render() === $renderedClassName) {
                return true;
            }
        }

        return false;
    }

    private function createUseStatement(ClassName $className): StatementInterface
    {
        return new Statement(
            new UseExpression(
                $className
            )
        );
    }
}
