<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ClosureExpression extends AbstractExpression
{
    private const RENDER_TEMPLATE = <<<'EOD'
(function () {
%s
})()
EOD;

    private CodeBlockInterface $codeBlock;

    public function __construct(CodeBlockInterface $codeBlock)
    {
        parent::__construct();

        $this->codeBlock = $codeBlock;
    }

    public function getCodeBlock(): CodeBlockInterface
    {
        return $this->codeBlock;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->codeBlock->getMetadata();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_TEMPLATE,
            $this->indent($this->codeBlock->render())
        );
    }

    private function indent(string $content): string
    {
        $lines = explode("\n", $content);

        array_walk($lines, function (&$line) {
            if ('' !== $line) {
                $line = '    ' . $line;
            }
        });

        return implode("\n", $lines);
    }
}
