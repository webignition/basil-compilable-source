<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\SourceInterface;

/**
 * @extends \Iterator<int, LineInterface>
 */
interface BlockInterface extends \Iterator, SourceInterface
{
    public function current(): LineInterface;
    public function key(): int;
    public function next(): void;
    public function rewind(): void;
    public function valid(): bool;
}
