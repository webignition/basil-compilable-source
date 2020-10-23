<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Construct\ReturnConstruct;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;

class ReturnExpression implements ExpressionInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE_NO_EXPRESSION = '{{ return_construct }}';
    private const RENDER_TEMPLATE = '{{ return_construct }} {{ expression_content }}';

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

    public function getTemplate(): string
    {
        return $this->expression instanceof ExpressionInterface
            ? self::RENDER_TEMPLATE
            : self::RENDER_TEMPLATE_NO_EXPRESSION;
    }

    public function getContext(): array
    {
        return [
            'return_construct' => (string) (new ReturnConstruct()),
            'expression_content' => $this->expression instanceof ExpressionInterface
                ? $this->expression
                : ''
        ];
    }
}
