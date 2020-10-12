<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Construct\ReturnConstruct;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;

class ReturnExpression implements ExpressionInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '{{ return_construct }}{{ expression_content }}';

    private ?ExpressionInterface $expression;

    public function __construct(?ExpressionInterface $expression = null)
    {
        $this->expression = $expression;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->expression instanceof ExpressionInterface
            ? $this->expression->getMetadata()
            : new Metadata();
    }

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    protected function getRenderContext(): array
    {
        $expressionContent = '';
        if ($this->expression instanceof ExpressionInterface) {
            $expressionContent = ' ' . $this->expression->render();
        }

        return [
            'return_construct' => (new ReturnConstruct())->render(),
            'expression_content' => $expressionContent,
        ];
    }
}
