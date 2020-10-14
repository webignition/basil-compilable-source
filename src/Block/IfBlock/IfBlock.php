<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\IfBlock;

use webignition\BasilCompilableSource\Block\AbstractBlock;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\Stubble\Resolvable;
use webignition\Stubble\ResolvableInterface;

class IfBlock extends AbstractBlock
{
    private const RENDER_TEMPLATE = <<<'EOD'
if ({{ expression }}) {
{{ body }}
}
EOD;

    private ExpressionInterface $expression;

    public function __construct(ExpressionInterface $expression, BodyInterface $body)
    {
        parent::__construct($body);

        $this->expression = $expression;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = $this->expression->getMetadata();
        return $metadata->merge(parent::getMetadata());
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'expression' => $this->expression->render(),
                'body' => $this->renderBody(),
            ]
        );
    }
}
