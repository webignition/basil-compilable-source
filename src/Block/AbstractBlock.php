<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\LineInterface;

abstract class AbstractBlock implements BlockInterface
{
    private int $iteratorIndex = 0;

    /**
     * @var LineInterface[]
     */
    private array $lines = [];

    /**
     * @param array<mixed> $lines
     */
    public function __construct(array $lines = [])
    {
        foreach ($lines as $line) {
            if ($this->canLineBeAdded($line)) {
                $this->lines[] = $line;
            }
        }
    }

    abstract public function canLineBeAdded(LineInterface $line): bool;

    /**
     * @return LineInterface[]
     */
    protected function getLines(): array
    {
        return $this->lines;
    }

    public function isEmpty(): bool
    {
        return 0 === count($this->lines);
    }

    public function render(): string
    {
        $renderedLines = [];

        foreach ($this as $line) {
            $renderedLines[] = $line->render();
        }

        return implode("\n", $renderedLines);
    }

    public function current(): LineInterface
    {
        return $this->lines[$this->iteratorIndex];
    }

    public function next(): void
    {
        ++$this->iteratorIndex;
    }

    public function key(): int
    {
        return $this->iteratorIndex;
    }

    public function valid(): bool
    {
        return isset($this->lines[$this->iteratorIndex]);
    }

    public function rewind(): void
    {
        $this->iteratorIndex = 0;
    }
}
