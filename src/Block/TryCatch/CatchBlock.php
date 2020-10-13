<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;

class CatchBlock extends AbstractBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
catch ({{ catch_expression }}) {
{{ body }}
}
EOD;

    private CatchExpression $catchExpression;

    public function __construct(CatchExpression $catchExpression, BodyInterface $body)
    {
        parent::__construct($body);

        $this->catchExpression = $catchExpression;
    }

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'catch_expression' => $this->catchExpression->render(),
                'body' => $this->renderBody(),
            ]
        );
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = parent::getMetadata();
        return $metadata->merge($this->catchExpression->getMetadata());
    }
}
