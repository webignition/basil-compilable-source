<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Block\CodeBlock;
use webignition\BasilCompilableSource\Line\CatchExpression;

class CatchBlock extends CodeBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
catch (%s) {
%s
}
EOD;

    private CatchExpression $catchExpression;

    public function __construct(CatchExpression $catchExpression, array $sources = [])
    {
        parent::__construct($sources);

        $this->catchExpression = $catchExpression;
    }

    public function render(): string
    {
        $lines = parent::render();
        $lines = $this->indent($lines);
        $lines = rtrim($lines, "\n");

        return sprintf(self::RENDER_TEMPLATE, $this->catchExpression->render(), $lines);
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
