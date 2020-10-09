<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

class TryBlock extends AbstractBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
try {
{{ body }}
}
EOD;

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    protected function getRenderContext(): array
    {
        return [
            'body' => $this->renderBody(),
        ];
    }
}
