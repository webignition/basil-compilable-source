<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Line\CatchExpression;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class CatchBlock extends AbstractBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
catch (%s) {
%s
}
EOD;

    private CatchExpression $catchExpression;

    public function __construct(CatchExpression $catchExpression, BodyInterface $body)
    {
        parent::__construct($body);

        $this->catchExpression = $catchExpression;
    }

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    protected function getAdditionalRenderComponents(): array
    {
        return [
            $this->catchExpression->render(),
        ];
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = parent::getMetadata();
        $metadata = $metadata->merge($this->catchExpression->getMetadata());

        return $metadata;
    }
}
