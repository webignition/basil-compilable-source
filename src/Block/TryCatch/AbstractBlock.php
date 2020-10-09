<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\Stubble\UnresolvedVariableException;
use webignition\Stubble\VariableResolver;

abstract class AbstractBlock implements HasMetadataInterface
{
    private BodyInterface $body;

    public function __construct(BodyInterface $body)
    {
        $this->body = $body;
    }

    abstract protected function getRenderTemplate(): string;

    /**
     * @return array<string, string>
     */
    abstract protected function getRenderContext(): array;

    public function getMetadata(): MetadataInterface
    {
        return $this->body->getMetadata();
    }

    public function render(): string
    {
        try {
            return VariableResolver::resolveTemplate(
                $this->getRenderTemplate(),
                $this->getRenderContext()
            );
        } catch (UnresolvedVariableException $unresolvedVariableException) {
            return $unresolvedVariableException->getTemplate();
        }
    }

    protected function renderBody(): string
    {
        $renderedBody = $this->body->render();

        $renderedBody = $this->indent($renderedBody);
        return rtrim($renderedBody, "\n");
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
