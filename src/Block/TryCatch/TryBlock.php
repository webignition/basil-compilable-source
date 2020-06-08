<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

class TryBlock extends AbstractBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
try {
%s
}
EOD;

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    protected function getAdditionalRenderComponents(): array
    {
        return [];
    }
}
