<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\Stubble\Resolvable;
use webignition\Stubble\ResolvableInterface;

class TryBlock extends AbstractBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
try {
{{ body }}
}
EOD;

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'body' => $this->renderBody(),
            ]
        );
    }
}
