<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\LineInterface;

class DocBlock extends AbstractBlock implements BlockInterface
{
    private const RENDER_TEMPLATE = '/**%s' . "\n" . ' */';

    public function canLineBeAdded(LineInterface $line): bool
    {
        return $line instanceof SingleLineComment || $line instanceof EmptyLine;
    }

    public function render(): string
    {
        $renderedLines = [];

        foreach ($this->getLines() as $line) {
            $renderedLine = "\n" . ' *';

            if ($line instanceof SingleLineComment) {
                $renderedLine .= ' ' . $line->getContent();
            }

            $renderedLines[] = $renderedLine;
        }

        return sprintf(self::RENDER_TEMPLATE, implode('', $renderedLines));
    }
}
