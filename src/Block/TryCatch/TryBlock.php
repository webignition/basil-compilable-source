<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Block\CodeBlock;

class TryBlock extends CodeBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
try {
%s
}
EOD;

    public function render(): string
    {
        $lines = parent::render();
        $lines = $this->indent($lines);
        $lines = rtrim($lines, "\n");

        return sprintf(self::RENDER_TEMPLATE, $lines);
    }

    private function indent(string $content): string
    {
        if ('' === $content) {
            return '';
        }

        $lines = explode("\n", $content);

        array_walk($lines, function (&$line) {
            if ('' !== $line) {
                $line = '    ' . $line;
            }
        });

        return implode("\n", $lines);
    }
}
