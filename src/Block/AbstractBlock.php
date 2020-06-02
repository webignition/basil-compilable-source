<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\LineInterface;

abstract class AbstractBlock implements BlockInterface
{
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

    public function getLines(): array
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

        foreach ($this->getLines() as $line) {
            /* @var LineInterface $line */
            $renderedLines[] = $line->render();
        }

        return implode("\n", $renderedLines);
    }
}
