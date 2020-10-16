<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Block\AbstractBlock;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Expression\CatchExpression;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;

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

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
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
