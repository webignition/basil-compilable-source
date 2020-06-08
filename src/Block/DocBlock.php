<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\SourceInterface;

class DocBlock implements SourceInterface
{
    private const RENDER_TEMPLATE = '/**' . "\n" . '%s */';

    /**
     * @var string[]
     */
    private array $lines = [];

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines)
    {
        foreach ($lines as $line) {
            if (is_string($line)) {
                $this->lines[] = $line;
            }
        }
    }

    public function render(): string
    {
        $renderedLines = $this->lines;

        array_walk($renderedLines, function (string &$renderedLine) {
            if ("\n" === $renderedLine) {
                $renderedLine = ' *' . "\n";
            } else {
                $renderedLine = ' * ' . $renderedLine . "\n";
            }
        });

        return sprintf(self::RENDER_TEMPLATE, implode('', $renderedLines));
    }
}
