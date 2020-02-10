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
        foreach ($lines as $line) {
            if ($line instanceof LineInterface) {
                $this->addLine($line);
            }
        }
    }

    abstract protected function canLineBeAdded(LineInterface $line): bool;

    public function addLine(LineInterface $line): void
    {
        if ($this->canLineBeAdded($line)) {
            $this->lines[] = $line;
        }
    }

    /**
     * @return \ArrayIterator<int, LineInterface>
     */
    public function getLines(): \ArrayIterator
    {
        return new \ArrayIterator($this->lines);
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
