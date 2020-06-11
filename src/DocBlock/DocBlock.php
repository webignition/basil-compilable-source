<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\DocBlock;

use webignition\BasilCompilableSource\Annotation\AnnotationInterface;
use webignition\BasilCompilableSource\SourceInterface;

class DocBlock implements SourceInterface
{
    private const RENDER_TEMPLATE = '/**' . "\n" . '%s */';

    /**
     * @var array<int, string|AnnotationInterface>
     */
    private array $lines = [];

    /**
     * @param array<int, string|AnnotationInterface> $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public function merge(DocBlock $docBlock): DocBlock
    {
        return new DocBlock(array_merge($this->lines, $docBlock->lines));
    }

    public function render(): string
    {
        $renderedLines = [];
        foreach ($this->lines as $line) {
            if (is_string($line)) {
                $renderedLines[] = $line;
            }

            if ($line instanceof AnnotationInterface) {
                $renderedLines[] = $line->render();
            }
        }

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
