<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Line\CatchExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class CatchBlock implements HasMetadataInterface
{
    private const RENDER_TEMPLATE = <<<'EOD'
catch (%s) {
%s
}
EOD;

    private CatchExpression $catchExpression;
    private BodyInterface $body;


    public function __construct(CatchExpression $catchExpression, BodyInterface $body)
    {
        $this->catchExpression = $catchExpression;
        $this->body = $body;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();
        $metadata = $metadata->merge($this->catchExpression->getMetadata());
        $metadata = $metadata->merge($this->body->getMetadata());

        return $metadata;
    }

    public function render(): string
    {
        $renderedBody = $this->body->render();

        $renderedBody = $this->indent($renderedBody);
        $renderedBody = rtrim($renderedBody, "\n");

        return sprintf(self::RENDER_TEMPLATE, $this->catchExpression->render(), $renderedBody);
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
