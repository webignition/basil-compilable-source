<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\DocBlock;

use webignition\BasilCompilableSource\Annotation\AnnotationInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\BasilCompilableSource\SourceInterface;

class DocBlock implements SourceInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE_EMPTY = <<<'EOD'
/**
 */
EOD;

    private const RENDER_TEMPLATE = <<<'EOD'
/**
{{ content }}
 */
EOD;

    /**
     * @var array<int, string|AnnotationInterface>
     */
    private array $lines;

    /**
     * @param array<int, string|AnnotationInterface> $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public function append(DocBlock $addition): self
    {
        return $this->merge($this, $addition);
    }

    public function prepend(DocBlock $addition): self
    {
        return $this->merge($addition, $this);
    }

    private function merge(DocBlock $source, DocBlock $addition): self
    {
        return new DocBlock(array_merge($source->lines, $addition->lines));
    }

    protected function getRenderTemplate(): string
    {
        if (0 === count($this->lines)) {
            return self::RENDER_TEMPLATE_EMPTY;
        }

        return self::RENDER_TEMPLATE;
    }

    protected function getRenderContext(): array
    {
        return [
            'content' => $this->renderContent(),
        ];
    }

    private function renderContent(): string
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
                $renderedLine = ' *';
            } else {
                $renderedLine = ' * ' . $renderedLine;
            }
        });

        return implode("\n", $renderedLines);
    }
}
