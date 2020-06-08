<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class TryBlock implements HasMetadataInterface
{
    private const RENDER_TEMPLATE = <<<'EOD'
try {
%s
}
EOD;

    private BodyInterface $body;

    public function __construct(BodyInterface $body)
    {
        $this->body = $body;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->body->getMetadata();
    }

    public function render(): string
    {
        $renderedBody = $this->body->render();

        $renderedBody = $this->indent($renderedBody);
        $renderedBody = rtrim($renderedBody, "\n");

        return sprintf(self::RENDER_TEMPLATE, $renderedBody);
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
