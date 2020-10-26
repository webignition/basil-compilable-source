<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\UseExpression;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\BasilCompilableSource\SourceInterface;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ClassDependencyCollection implements \Countable, SourceInterface, ResolvableProviderInterface
{
    use RenderTrait;

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

    public function merge(ClassDependencyCollection $collection): ClassDependencyCollection
    {
        return new ClassDependencyCollection(array_merge($this->classNames, $collection->classNames));
    }

    private function containsClassName(ClassName $className): bool
    {
        $renderedClassName = (string) $className;

        foreach ($this->classNames as $className) {
            if ((string) $className === $renderedClassName) {
                return true;
            }
        }

        return false;
    }

    public function count(): int
    {
        return count($this->classNames);
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function getResolvable(): ResolvableInterface
    {
        $classNamesToRender = array_filter($this->classNames, function (ClassName $className) {
            if (false === $className->isInRootNamespace()) {
                return true;
            }

            return is_string($className->getAlias());
        });

        $useStatementResolvables = [];
        foreach ($classNamesToRender as $className) {
            $useStatement = new Statement(new UseExpression($className));

            $useStatementResolvables[] = new ResolvedTemplateMutatorResolvable(
                $useStatement->getResolvable(),
                function (string $resolvedTemplate) {
                    return $this->useStatementResolvedTemplateMutator($resolvedTemplate);
                }
            );
        }

        return new ResolvedTemplateMutatorResolvable(
            ResolvableCollection::create($useStatementResolvables),
            function (string $resolvedTemplate) {
                return $this->resolvedTemplateMutator($resolvedTemplate);
            }
        );
    }

    private function resolvedTemplateMutator(string $resolvedTemplate): string
    {
        $lines = explode("\n", $resolvedTemplate);
        sort($lines);

        return implode("\n", array_filter($lines));
    }

    private function useStatementResolvedTemplateMutator(string $resolvedTemplate): string
    {
        return $resolvedTemplate . "\n";
    }
}
