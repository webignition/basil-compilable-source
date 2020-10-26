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
use webignition\StubbleResolvable\ResolvedTemplateMutationInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ClassDependencyCollection implements
    \Countable,
    SourceInterface,
    ResolvableInterface,
    ResolvedTemplateMutationInterface
{
    use RenderTrait;

    /**
     * @var ClassName[]
     */
    private array $classNames = [];

    private ?ResolvableInterface $resolvable = null;

    /**
     * @param ClassName[] $classNames
     */
    public function __construct(array $classNames = [])
    {
        foreach ($classNames as $className) {
            if ($className instanceof ClassName && false === $this->containsClassName($className)) {
                $this->classNames[] = $className;
            }
        }
    }

    public function merge(ClassDependencyCollection $collection): ClassDependencyCollection
    {
        return new ClassDependencyCollection(array_merge($this->classNames, $collection->classNames));
    }

    public function count(): int
    {
        return count($this->classNames);
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function getTemplate(): string
    {
        return $this->getResolvable()->getTemplate();
    }

    public function getContext(): array
    {
        return $this->resolvable->getContext();
    }

    public function getResolvedTemplateMutator(): callable
    {
        return function (string $resolvedTemplate): string {
            $lines = explode("\n", $resolvedTemplate);
            sort($lines);

            return implode("\n", array_filter($lines));
        };
    }

    /**
     * @return ClassName[]
     */
    public function getClassNames(): array
    {
        return $this->classNames;
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

    private function getResolvable(): ResolvableInterface
    {
        if (null === $this->resolvable) {
            $this->resolvable = $this->createResolvable();
        }

        return $this->resolvable;
    }

    private function createResolvable(): ResolvableInterface
    {
        $useStatementResolvables = [];
        foreach ($this->classNames as $className) {
            $useStatement = new Statement(new UseExpression($className));

            $useStatementResolvables[] = new ResolvedTemplateMutatorResolvable(
                $useStatement,
                function (string $resolvedTemplate) {
                    return $this->useStatementResolvedTemplateMutator($resolvedTemplate);
                }
            );
        }

        return ResolvableCollection::create($useStatementResolvables);
    }

    private function useStatementResolvedTemplateMutator(string $resolvedTemplate): string
    {
        return $resolvedTemplate . "\n";
    }
}
