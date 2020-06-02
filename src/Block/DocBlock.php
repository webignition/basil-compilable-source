<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\Literal;
use webignition\BasilCompilableSource\LineInterface;

class DocBlock extends AbstractBlock implements BlockInterface
{
    private const RENDER_TEMPLATE = '/**%s' . "\n" . ' */';

    public function canLineBeAdded(LineInterface $line): bool
    {
        return $line instanceof EmptyLine || $line instanceof Literal;
    }

    public function render(): string
    {
        $renderedContent = parent::render();
        if ('' === $renderedContent) {
            return sprintf(self::RENDER_TEMPLATE, '');
        }

        $renderedLines = explode("\n", $renderedContent);

        array_walk($renderedLines, function (string &$renderedLine) {
            $prefix = "\n" . ' *';

            $renderedLine = '' === $renderedLine
                ? $prefix
                : $prefix . ' ' . $renderedLine;
        });

        return sprintf(self::RENDER_TEMPLATE, implode('', $renderedLines));
    }
}
