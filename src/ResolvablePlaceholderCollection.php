<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

/**
 * @implements \IteratorAggregate<ResolvableVariablePlaceholderInterface>
 */
class ResolvablePlaceholderCollection implements \IteratorAggregate
{
    private string $placeholderType;

    /**
     * @var ResolvableVariablePlaceholderInterface[]
     */
    private array $variablePlaceholders = [];

    private function __construct(string $placeholderType)
    {
        $this->placeholderType = ResolvablePlaceholder::isAllowedType($placeholderType)
            ? $placeholderType
            : ResolvablePlaceholder::TYPE_EXPORT;
    }

    /**
     * @param string $placeholderType
     * @param string[] $names
     *
     * @return ResolvablePlaceholderCollection
     */
    public static function create(string $placeholderType, array $names = []): ResolvablePlaceholderCollection
    {
        $collection = new ResolvablePlaceholderCollection($placeholderType);

        foreach ($names as $name) {
            if (is_string($name)) {
                $collection->add(new ResolvablePlaceholder($name, $collection->getPlaceholderType()));
            }
        }

        return $collection;
    }

    /**
     * @param string[] $names
     *
     * @return ResolvablePlaceholderCollection
     */
    public static function createDependencyCollection(array $names = []): ResolvablePlaceholderCollection
    {
        return self::create(ResolvablePlaceholder::TYPE_DEPENDENCY, $names);
    }

    /**
     * @param string[] $names
     *
     * @return ResolvablePlaceholderCollection
     */
    public static function createExportCollection(array $names = []): ResolvablePlaceholderCollection
    {
        return self::create(ResolvablePlaceholder::TYPE_EXPORT, $names);
    }

    public function createPlaceholder(string $name): ResolvableVariablePlaceholderInterface
    {
        $variablePlaceholder = $this->variablePlaceholders[$name] ?? null;

        if (null === $variablePlaceholder) {
            $variablePlaceholder = new ResolvablePlaceholder($name, $this->placeholderType);
            $this->add($variablePlaceholder);
        }

        return $variablePlaceholder;
    }

    public function getPlaceholderType(): string
    {
        return $this->placeholderType;
    }

    public function merge(ResolvablePlaceholderCollection $collection): ResolvablePlaceholderCollection
    {
        $new = clone $this;

        if ($collection->getPlaceholderType() === $new->getPlaceholderType()) {
            foreach ($collection as $variablePlaceholder) {
                $new->add($variablePlaceholder);
            }
        }

        return $new;
    }

    public function add(ResolvableVariablePlaceholderInterface $variablePlaceholder): void
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
