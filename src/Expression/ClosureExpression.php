<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;

class ClosureExpression extends AbstractExpression implements RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = <<<'EOD'
(function () {
{{ body }}
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

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'body' => $this->indent($this->body->render()),
            ]
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
