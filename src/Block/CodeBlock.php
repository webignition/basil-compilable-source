<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Line\ClosureExpression;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\Line\Statement\StatementInterface;
use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\SourceInterface;

class CodeBlock extends AbstractBlock implements CodeBlockInterface
{
    /**
     * @param SourceInterface[] $sources
     */
    public function __construct(array $sources = [])
    {
        $lines = [];

        foreach ($sources as $source) {
            if ($source instanceof LineInterface) {
                $lines[] = $source;
            }

            if ($source instanceof CodeBlockInterface) {
                foreach ($source as $codeBlockLine) {
                    $lines[] = $codeBlockLine;
                }
            }
        }

        parent::__construct($lines);
    }

    public static function createEnclosingCodeBlock(BodyInterface $body): self
    {
        return new CodeBlock([
            new Statement(
                new ClosureExpression($body)
            ),
        ]);
    }

    public function canLineBeAdded(LineInterface $line): bool
    {
        if ($line instanceof StatementInterface || $line instanceof SingleLineComment || $line instanceof EmptyLine) {
            return true;
        }

        return false;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this as $line) {
            if ($line instanceof ExpressionInterface || $line instanceof StatementInterface) {
                $metadata = $metadata->merge($line->getMetadata());
            }
        }

        return $metadata;
    }
}
