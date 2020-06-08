<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ClosureExpression extends AbstractExpression
{
    private const RENDER_TEMPLATE = <<<'EOD'
(function () {
%s
})()
EOD;

    private BodyInterface $body;

    public function __construct(BodyInterface $body)
    {
        parent::__construct();

        $this->body = $body;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->body->getMetadata();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_TEMPLATE,
            $this->indent($this->body->render())
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
