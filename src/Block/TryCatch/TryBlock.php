<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;

class TryBlock extends AbstractBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
try {
{{ body }}
}
EOD;

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'body' => $this->renderBody(),
            ]
        );
    }
}
