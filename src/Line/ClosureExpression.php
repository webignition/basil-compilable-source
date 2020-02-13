<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Line\Statement\StatementInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ClosureExpression extends AbstractExpression
{
    private const RENDER_TEMPLATE = <<<'EOD'
(function () {
%s%s
})()
EOD;

    private $codeBlock;

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
        $codeBlockLines = $this->codeBlock->getLines();

        $renderedBodyStatements = '';
        $renderedFinalStatement = '';

        if (count($codeBlockLines) > 0) {
            $finalStatement = array_pop($codeBlockLines);
            $bodyStatements = $codeBlockLines;

            $renderedBodyStatements = (string) array_reduce(
                $bodyStatements,
                function (?string $content, StatementInterface $statement) {
                    return $content . $statement->render();
                }
            );

            $renderedBodyStatements = $this->indent($renderedBodyStatements);
            $renderedFinalStatement = $this->indent($finalStatement->render());
        }

        if ('' !== $renderedBodyStatements) {
            $renderedBodyStatements .= "\n\n";
        }

        return sprintf(
            self::RENDER_TEMPLATE,
            $renderedBodyStatements,
            $renderedFinalStatement
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
