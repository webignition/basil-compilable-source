<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\LineInterface;

abstract class AbstractBlock implements BlockInterface
{
    /**
     * @var LineInterface[]
     */
    private $lines = [];

    /**
     * @param array<mixed> $lines
     */
    public function __construct(array $lines = [])
    {
        $this->addLines($lines);
    }

    abstract public function canLineBeAdded(LineInterface $line): bool;

    public function addLine(LineInterface $line): void
    {
        if ($this->canLineBeAdded($line)) {
            $this->lines[] = $line;
        }
    }

    public function addLines(array $lines): void
    {
        foreach ($lines as $line) {
            if ($line instanceof LineInterface) {
                $this->addLine($line);
            }
        }
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function addBlock(BlockInterface $block): void
    {
        $this->addLines($block->getLines());
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
