<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

/**
 * @implements \IteratorAggregate<VariablePlaceholder>
 */
class VariablePlaceholderCollection implements \IteratorAggregate
{
    /**
     * @var VariablePlaceholder[]
     */
    private $variablePlaceholders = [];

    /**
     * @param VariablePlaceholder[] $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param string[] $names
     *
     * @return VariablePlaceholderCollection
     */
    public static function create(array $names): VariablePlaceholderCollection
    {
        $collection = new VariablePlaceholderCollection();

        foreach ($names as $name) {
            if (is_string($name)) {
                $collection->createPlaceholder($name);
            }
        }

        return $collection;
    }

    public function createPlaceholder(string $name): VariablePlaceholder
    {
        $variablePlaceholder = $this->variablePlaceholders[$name] ?? null;

        if (null === $variablePlaceholder) {
            $variablePlaceholder = new VariablePlaceholder($name);
            $this->add($variablePlaceholder);
        }

        return $variablePlaceholder;
    }

    public function merge(VariablePlaceholderCollection $collection): void
    {
        foreach ($collection as $variablePlaceholder) {
            $this->add($variablePlaceholder);
        }
    }

    public function add(VariablePlaceholder $variablePlaceholder): void
    {
        $name = $variablePlaceholder->getContent();

        if (!array_key_exists($name, $this->variablePlaceholders)) {
            $this->variablePlaceholders[$name] = $variablePlaceholder;
        }
    }

    // IteratorAggregate methods

    public function getIterator()
    {
        return new \ArrayIterator($this->variablePlaceholders);
    }
}
