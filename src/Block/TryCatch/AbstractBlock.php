<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

abstract class AbstractBlock implements HasMetadataInterface
{
    private BodyInterface $body;

    public function __construct(BodyInterface $body)
    {
        $this->body = $body;
    }

    abstract protected function getRenderTemplate(): string;

    /**
     * @return string[]
     */
    abstract protected function getAdditionalRenderComponents(): array;

    public function getMetadata(): MetadataInterface
    {
        return $this->body->getMetadata();
    }

    public function render(): string
    {
        $renderedBody = $this->body->render();

        $renderedBody = $this->indent($renderedBody);
        $renderedBody = rtrim($renderedBody, "\n");

        return sprintf(
            $this->getRenderTemplate(),
            ...array_merge($this->getAdditionalRenderComponents(), [$renderedBody])
        );
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
