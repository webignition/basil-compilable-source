<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

/**
 * @implements \IteratorAggregate<VariablePlaceholder>
 */
class VariablePlaceholderCollection implements \IteratorAggregate
{
    private string $placeholderType;

    /**
     * @var VariablePlaceholder[]
     */
    private array $variablePlaceholders = [];

    private function __construct(string $placeholderType)
    {
        $this->placeholderType = VariablePlaceholder::isAllowedType($placeholderType)
            ? $placeholderType
            : VariablePlaceholder::TYPE_EXPORT;
    }

    /**
     * @param string $placeholderType
     * @param string[] $names
     *
     * @return VariablePlaceholderCollection
     */
    public static function create(string $placeholderType, array $names = []): VariablePlaceholderCollection
    {
        $collection = new VariablePlaceholderCollection($placeholderType);

        foreach ($names as $name) {
            if (is_string($name)) {
                $collection->add(new VariablePlaceholder($name, $collection->getPlaceholderType()));
            }
        }

        return $collection;
    }

    /**
     * @param string[] $names
     *
     * @return VariablePlaceholderCollection
     */
    public static function createDependencyCollection(array $names = []): VariablePlaceholderCollection
    {
        return self::create(VariablePlaceholder::TYPE_DEPENDENCY, $names);
    }

    /**
     * @param string[] $names
     *
     * @return VariablePlaceholderCollection
     */
    public static function createExportCollection(array $names = []): VariablePlaceholderCollection
    {
        return self::create(VariablePlaceholder::TYPE_EXPORT, $names);
    }

    public function createPlaceholder(string $name): VariablePlaceholder
    {
        $variablePlaceholder = $this->variablePlaceholders[$name] ?? null;

        if (null === $variablePlaceholder) {
            $variablePlaceholder = new VariablePlaceholder($name, $this->placeholderType);
            $this->add($variablePlaceholder);
        }

        return $variablePlaceholder;
    }

    public function getPlaceholderType(): string
    {
        return $this->placeholderType;
    }

    public function merge(VariablePlaceholderCollection $collection): void
    {
        if ($collection->getPlaceholderType() === $this->getPlaceholderType()) {
            foreach ($collection as $variablePlaceholder) {
                $this->add($variablePlaceholder);
            }
        }
    }

    public function add(VariablePlaceholder $variablePlaceholder): void
    {
        if ($variablePlaceholder->getType() === $this->getPlaceholderType()) {
            $name = $variablePlaceholder->getName();

            if (!array_key_exists($name, $this->variablePlaceholders)) {
                $this->variablePlaceholders[$name] = $variablePlaceholder;
            }
        }
    }

    // IteratorAggregate methods

    public function getIterator()
    {
        return new \ArrayIterator($this->variablePlaceholders);
    }
}
