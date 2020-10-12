<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;

class Statement implements StatementInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '{{ expression }};';

    private ExpressionInterface $expression;

    public function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->expression;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->expression->getMetadata();
    }

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    protected function getRenderContext(): array
    {
        return [
            'expression' => $this->expression->render(),
        ];
    }
}
