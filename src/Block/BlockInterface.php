<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\SourceInterface;

interface BlockInterface extends SourceInterface
{
    public function addLine(LineInterface $line): void;

    /**
     * @param LineInterface[] $lines
     */
    public function addLines(array $lines): void;

    /**
     * @return array<int, LineInterface>
     */
    public function getLines(): array;
}
