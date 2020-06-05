<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

/**
 * @implements \IteratorAggregate<VariableDependencyInterface>
 */
class VariableDependencyCollection implements \IteratorAggregate
{
    private string $placeholderType;

    /**
     * @var VariableDependencyInterface[]
     */
    private array $dependencies = [];

    private function __construct(string $placeholderType)
    {
        $this->placeholderType = VariableDependency::isAllowedType($placeholderType)
            ? $placeholderType
            : VariableDependency::TYPE_EXPORT;
    }

    /**
     * @param string $placeholderType
     * @param string[] $names
     *
     * @return VariableDependencyCollection
     */
    public static function create(string $placeholderType, array $names = []): VariableDependencyCollection
    {
        $collection = new VariableDependencyCollection($placeholderType);

        foreach ($names as $name) {
            if (is_string($name)) {
                $collection->add(new VariableDependency($name, $collection->getPlaceholderType()));
            }
        }

        return $collection;
    }

    /**
     * @param string[] $names
     *
     * @return VariableDependencyCollection
     */
    public static function createDependencyCollection(array $names = []): VariableDependencyCollection
    {
        return self::create(VariableDependency::TYPE_DEPENDENCY, $names);
    }

    /**
     * @param string[] $names
     *
     * @return VariableDependencyCollection
     */
    public static function createExportCollection(array $names = []): VariableDependencyCollection
    {
        return self::create(VariableDependency::TYPE_EXPORT, $names);
    }

    public function createPlaceholder(string $name): VariableDependencyInterface
    {
        $dependency = $this->dependencies[$name] ?? null;

        if (null === $dependency) {
            $dependency = new VariableDependency($name, $this->placeholderType);
            $this->add($dependency);
        }

        return $dependency;
    }

    public function getPlaceholderType(): string
    {
        return $this->placeholderType;
    }

    public function merge(VariableDependencyCollection $collection): VariableDependencyCollection
    {
        $new = clone $this;

        if ($collection->getPlaceholderType() === $new->getPlaceholderType()) {
            foreach ($collection as $dependency) {
                $new->add($dependency);
            }
        }

        return $new;
    }

    public function add(VariableDependencyInterface $dependency): void
    {
        if ($dependency->getType() === $this->getPlaceholderType()) {
            $name = $dependency->getName();

            if (!array_key_exists($name, $this->dependencies)) {
                $this->dependencies[$name] = $dependency;
            }
        }
    }

    // IteratorAggregate methods

    public function getIterator()
    {
        return new \ArrayIterator($this->dependencies);
    }
}
